<?php
################################################################################
#                                                                              #
# Webmoney XML Interfaces parser by DKameleon (http://dkameleon.com)           #
#                                                                              #
# Updates and new versions: http://my-tools.net/wmxi/                          #
#                                                                              #
# Server requirements:                                                         #
#  - Enabled XML support                                                       #
#                                                                              #
# History of changes:                                                          #
# 2007.02.24                                                                   #
# - initial release                                                            #
# 2007.04.19                                                                   #
# - set up default parser encoding to UTF-8                                    #
# - added encoding conversion support                                          #
# 2007.04.21                                                                   #
# - changed parser mechanism to more flexible one                              #
#                                                                              #
################################################################################


# WMXIParser class
class WMXIParser {

	var $parser_encoding = "UTF-8";
	var $parser;
	var $error_code;
	var $error_string;
	var $current_line;
	var $current_column;
	var $datas = array();
	var $data = array();


	function _tagOpen($parser, $tag, $attribs) {
		$node = array(
			'name' => strtolower($tag),
			'data' => '',
		);
		if (count($attribs) > 0) { $node["@"] = $attribs; }
		$this->data['node'][] = $node;
		$this->datas[] =& $this->data;
		$this->data =& $this->data['node'][count($this->data['node'])-1];
	}


	function _tagClose($parser, $tag) {
		$this->data =& $this->datas[count($this->datas)-1];
		array_pop($this->datas);
	}


	function _tagData($parser, $cdata) {
		$this->data['data'] .= $cdata;
	}


	function _change_encoding($data, $encoding) {
		$result = array();

		foreach($data as $k => $v) {
			$value = is_array($v) ? $this->_change_encoding($v, $encoding) : mb_convert_encoding($v, $encoding, $this->parser_encoding);
			$result[$k] = $value;
		}

		return $result;
	}


	function Parse($data, $encoding = "UTF-8") {
		if (!$this->parser = @xml_parser_create($this->parser_encoding)) {
			$this->parser = xml_parser_create();
		}
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
		xml_set_element_handler($this->parser, '_tagOpen', '_tagClose');
		xml_set_character_data_handler($this->parser, '_tagData');
		if (!xml_parse($this->parser, $data)) {
			$this->data = array();
			$this->error_code = xml_get_error_code($this->parser);
			$this->error_string = xml_error_string($this->error_code);
			$this->current_line = xml_get_current_line_number($this->parser);
			$this->current_column = xml_get_current_column_number($this->parser);
		} else {
			$this->data = $this->data['node'];
		}
		xml_parser_free($this->parser);
		$this->data = $this->_change_encoding($this->data, $encoding);
		return $this->data;
	}


	function Reindex($data, $skip_attr = false) {
		$result = array();

		foreach($data as $k => $v) {
			$name = $v["name"];
			if ($skip_attr) {
				$result[$name] = isset($v["node"]) ? $this->Reindex($v["node"], $skip_attr) : $v["data"];
			} else {
				if (isset($v["@"]) && !$skip_attr) { $result[$name]["@"] = $v["@"]; }
				$result[$name]["data"] = isset($v["node"]) ? $this->Reindex($v["node"], $skip_attr) : $v["data"];
			}
		}

		return $result;
	}


}
# WMXIParser class

?>