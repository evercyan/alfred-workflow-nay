#!/usr/bin/env ruby
%w|tempfile base64 uri net/http openssl json cgi fileutils open3|.each(&method(:require))

def dump_clipboard_image
    file = Tempfile.new ['alfred_ocr', '.jpg']
    file.close

    `./src/bin/pngpaste #{file.path}`
    raise '请先复制图片到剪贴板' unless $?.success?

    content = Base64.encode64 file.open.read
    raise '图片必须小于 4M' if content.length > 4*1024*1024

    content
ensure
    file.close
    file.unlink
end

CREDENTIALS_FOLDER = (ENV['alfred_workflow_data'] or ENV['HOME'])
CREDENTIALS_PATH = CREDENTIALS_FOLDER + '/.alfred_ocr_credentials'

def get_credentials
    credentials = {}
    FileUtils.mkdir_p CREDENTIALS_FOLDER
    api_key = ENV['bd_ocr_key']
    api_secret = ENV['bd_ocr_secret']
    begin
        credentials = Marshal.load IO.binread CREDENTIALS_PATH
        raise '证书过期' if credentials['expires_at'] < Time.now
    rescue
        raise '请先配置 bd_ocr_key' unless api_key
        raise '请先配置 bd_ocr_secret' unless api_secret
        url = URI("https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id=#{api_key}&client_secret=#{api_secret}")

        http = Net::HTTP.new(url.host, url.port)
        http.use_ssl = true
        http.verify_mode = OpenSSL::SSL::VERIFY_NONE

        request = Net::HTTP::Post.new(url)

        response = http.request(request)
        credentials = JSON.load response.read_body
        raise (credentials['error_msg'] or 'Credentials incorrect') unless credentials['expires_in']
        credentials['expires_at'] = Time.now + credentials['expires_in']
        IO.binwrite CREDENTIALS_PATH, Marshal.dump(credentials)
    end
    credentials
end

def clear_credentials
    FileUtils.rm CREDENTIALS_PATH rescue nil
end

def ocr_text(image_base64, credentials)
    image_base64_encoded = CGI::escape image_base64
    access_token = credentials['access_token']

    url = URI("https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token=#{access_token}")

    http = Net::HTTP.new(url.host, url.port)
    http.use_ssl = true
    http.verify_mode = OpenSSL::SSL::VERIFY_NONE

    request = Net::HTTP::Post.new(url)
    request['content-type'] = 'application/x-www-form-urlencoded'
    request.body = "image=#{image_base64_encoded}"

    response = http.request(request)
    data = JSON.load response.read_body
    raise (data['error_msg'] or '请求失败') unless data['words_result']
    data['words_result'].map{|x| x['words']}.join "\n"
end

def copy(str)
    Open3.popen3( 'pbcopy' ){ |input, _, _| input << str }
end

def alfred_output(variables)
    obj = {
        'alfredworkflow' => {
            'arg' => 'ocr',
            'config' => {},
            'variables' => variables
        }
    }
    puts(JSON.dump obj)
end

begin
    image_base64 = dump_clipboard_image
    credentials = get_credentials
    result = ''
    times = 0
    a = proc{redo}
    begin
        result = ocr_text image_base64, credentials
    rescue
        STDERR.puts '请求失败'
        STDERR.puts $!
        STDERR.puts $!.stack
        # clear credentials cache and redo the entire workflow
        clear_credentials
        a.call
        times += 1
        retry if times <= 1
    end
    copy result
    alfred_output({
        'title' => '文字识别',
        'content' => result
    })
rescue
    alfred_output({
        'title' => '文字识别',
        'content' => "#{$!.message}"
    })
    STDERR.puts $!
    STDERR.puts $!.stack
end
