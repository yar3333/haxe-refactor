<?php

interface tjson_EncodeStyle {
	function beginObject($depth);
	function endObject($depth);
	function beginArray($depth);
	function endArray($depth);
	function firstEntry($depth);
	function entrySeperator($depth);
	function keyValueSeperator($depth);
}
