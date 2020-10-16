#!/usr/bin/env ruby

keyword = ARGV[0]
processes = `ps -A -o pid -o %cpu -o comm | grep -i [^/]*#{keyword}[^/]*$`.split("\n")
xmlString = "<?xml version=\"1.0\"?>\n<items>\n"
processes.each do | process |
	processId, processCpu, processPath = process.match(/(\d+)\s+(\d+\.\d+)\s+(.*)/).captures
	processName = processPath.match(/[^\/]*#{keyword}[^\/]*$/i)
	iconValue = processPath.match(/.*\.app\//)
	iconType = "fileicon"
	if !iconValue
		iconValue = "/System/Library/CoreServices/CoreTypes.bundle/Contents/Resources/ExecutableBinaryIcon.icns"
		iconType = ""
	end
	thisXmlString = "\t<item uid=\"#{processName}\" arg=\"#{processId}\">
		<title>#{processName}</title>
		<subtitle>#{processCpu}% CPU @ #{processPath}</subtitle>
		<icon type=\"#{iconType}\">#{iconValue}</icon>
	</item>\n"
	xmlString += thisXmlString
end
xmlString += "</items>"
puts xmlString