var FNVCraft = {}; // namespace
FNVCraft._dbh = {}; // handler
FNVCraft._dbh.db = null;


FNVCraft.sprintf = function() {
  //  discuss at: http://phpjs.org/functions/sprintf/
  // original by: Ash Searle (http://hexmen.com/blog/)
  // improved by: Michael White (http://getsprink.com)
  // improved by: Jack
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Dj
  // improved by: Allidylls
  //    input by: Paulo Freitas
  //    input by: Brett Zamir (http://brett-zamir.me)
  //   example 1: sprintf("%01.2f", 123.1);
  //   returns 1: 123.10
  //   example 2: sprintf("[%10s]", 'monkey');
  //   returns 2: '[    monkey]'
  //   example 3: sprintf("[%'#10s]", 'monkey');
  //   returns 3: '[####monkey]'
  //   example 4: sprintf("%d", 123456789012345);
  //   returns 4: '123456789012345'
  //   example 5: sprintf('%-03s', 'E');
  //   returns 5: 'E00'

  var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
  var a = arguments;
  var i = 0;
  var format = a[i++];

  // pad()
  var pad = function(str, len, chr, leftJustify) {
	 if (!chr) {
		chr = ' ';
	 }
	 var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0)
		.join(chr);
	 return leftJustify ? str + padding : padding + str;
  };

  // justify()
  var justify = function(value, prefix, leftJustify, minWidth, zeroPad, customPadChar) {
	 var diff = minWidth - value.length;
	 if (diff > 0) {
		if (leftJustify || !zeroPad) {
		  value = pad(value, minWidth, customPadChar, leftJustify);
		} else {
		  value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
		}
	 }
	 return value;
  };

  // formatBaseX()
  var formatBaseX = function(value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
	 // Note: casts negative numbers to positive ones
	 var number = value >>> 0;
	 prefix = prefix && number && {
		'2': '0b',
		'8': '0',
		'16': '0x'
	 }[base] || '';
	 value = prefix + pad(number.toString(base), precision || 0, '0', false);
	 return justify(value, prefix, leftJustify, minWidth, zeroPad);
  };

  // formatString()
  var formatString = function(value, leftJustify, minWidth, precision, zeroPad, customPadChar) {
	 if (precision != null) {
		value = value.slice(0, precision);
	 }
	 return justify(value, '', leftJustify, minWidth, zeroPad, customPadChar);
  };

  // doFormat()
  var doFormat = function(substring, valueIndex, flags, minWidth, _, precision, type) {
	 var number, prefix, method, textTransform, value;

	 if (substring === '%%') {
		return '%';
	 }

	 // parse flags
	 var leftJustify = false;
	 var positivePrefix = '';
	 var zeroPad = false;
	 var prefixBaseX = false;
	 var customPadChar = ' ';
	 var flagsl = flags.length;
	 for (var j = 0; flags && j < flagsl; j++) {
		switch (flags.charAt(j)) {
		  case ' ':
			 positivePrefix = ' ';
			 break;
		  case '+':
			 positivePrefix = '+';
			 break;
		  case '-':
			 leftJustify = true;
			 break;
		  case "'":
			 customPadChar = flags.charAt(j + 1);
			 break;
		  case '0':
			 zeroPad = true;
			 customPadChar = '0';
			 break;
		  case '#':
			 prefixBaseX = true;
			 break;
		}
	 }

	 // parameters may be null, undefined, empty-string or real valued
	 // we want to ignore null, undefined and empty-string values
	 if (!minWidth) {
		minWidth = 0;
	 } else if (minWidth === '*') {
		minWidth = +a[i++];
	 } else if (minWidth.charAt(0) == '*') {
		minWidth = +a[minWidth.slice(1, -1)];
	 } else {
		minWidth = +minWidth;
	 }

	 // Note: undocumented perl feature:
	 if (minWidth < 0) {
		minWidth = -minWidth;
		leftJustify = true;
	 }

	 if (!isFinite(minWidth)) {
		throw new Error('sprintf: (minimum-)width must be finite');
	 }

	 if (!precision) {
		precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type === 'd') ? 0 : undefined;
	 } else if (precision === '*') {
		precision = +a[i++];
	 } else if (precision.charAt(0) == '*') {
		precision = +a[precision.slice(1, -1)];
	 } else {
		precision = +precision;
	 }

	 // grab value using valueIndex if required?
	 value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

	 switch (type) {
		case 's':
		  return formatString(String(value), leftJustify, minWidth, precision, zeroPad, customPadChar);
		case 'c':
		  return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
		case 'b':
		  return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'o':
		  return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'x':
		  return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'X':
		  return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad)
			 .toUpperCase();
		case 'u':
		  return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
		case 'i':
		case 'd':
		  number = +value || 0;
		  number = Math.round(number - number % 1); // Plain Math.round doesn't just truncate
		  prefix = number < 0 ? '-' : positivePrefix;
		  value = prefix + pad(String(Math.abs(number)), precision, '0', false);
		  return justify(value, prefix, leftJustify, minWidth, zeroPad);
		case 'e':
		case 'E':
		case 'f': // Should handle locales (as per setlocale)
		case 'F':
		case 'g':
		case 'G':
		  number = +value;
		  prefix = number < 0 ? '-' : positivePrefix;
		  method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
		  textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
		  value = prefix + Math.abs(number)[method](precision);
		  return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
		default:
		  return substring;
	 }
  };

  return format.replace(regex, doFormat);
}

FNVCraft.empty = function (mixed_var) {
  //  discuss at: http://phpjs.org/functions/empty/
  // original by: Philippe Baumann
  //    input by: Onno Marsman
  //    input by: LH
  //    input by: Stoyan Kyosev (http://www.svest.org/)
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Onno Marsman
  // improved by: Francesco
  // improved by: Marc Jansen
  // improved by: Rafal Kukawski
  //   example 1: empty(null);
  //   returns 1: true
  //   example 2: empty(undefined);
  //   returns 2: true
  //   example 3: empty([]);
  //   returns 3: true
  //   example 4: empty({});
  //   returns 4: true
  //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
  //   returns 5: false

  var undef, key, i, len;
  var emptyValues = [undef, null, false, 0, '', '0'];

  for (i = 0, len = emptyValues.length; i < len; i++) {
    if (mixed_var === emptyValues[i]) {
      return true;
    }
  }

  if (typeof mixed_var === 'object') {
    for (key in mixed_var) {
      // TODO: should we check for own properties only?
      //if (mixed_var.hasOwnProperty(key)) {
      return false;
      //}
    }
    return true;
  }

  return false;
}


FNVCraft.log = function( msg , type) {
	var d = new Date();
	
	if ('ferr' == type) {
		msg += "\nFNVCraft was forced to stop !";
	}
	msg = msg.replace(/\n/g, "\n               ");
	
	jQuery('#logs').prepend( 
		FNVCraft.sprintf('%02d', d.getHours())+':'
		+FNVCraft.sprintf('%02d', d.getMinutes())+':'
		+FNVCraft.sprintf('%02d', d.getSeconds())+"."
		+FNVCraft.sprintf('%03d', d.getMilliseconds())
		+' - '
		+((type) ? '<font class="'+type+'">' : '')
		+msg
		+((type) ? '</font>' : '')+"\n"
	);
};

FNVCraft._dbh.onError = function(event) {
	var err = event.target.error.message;
	if (FNVCraft.empty( err )) {
		FNVCraft.log("Woops, something went terribly wrong with IndexedDB !", 'ferr');
	} else {
		if ( 'InvalidStateError' == event.target.error.name )
			FNVCraft.log('IndexedDB Error: '+err, 'ferr');
		else
			FNVCraft.log('IndexedDB Error: '+err, 'err');
	}
};

FNVCraft._dbh.doQuery = function(sql) {

};

FNVCraft._dbh.open = function() {

	var _version = 3;
	
	// Opera
	// var request = window.indexedDB.open("FNVCraftDB", _version);
	
	//ff call
	//https://developer.mozilla.org/en-US/docs/Web/API/IDBFactory.open
	var request = window.indexedDB.open("FNVCraftDB", {version: _version, storage: "temporary"});
	
	// IE ... fails!!!
	
	request.onsuccess = function(event) {
		FNVCraft._dbh.db = event.target.result;
		FNVCraft.log('Database connection successfull', 'confirm');
	};
	
	request.onupgradeneeded = function(event) {
		FNVCraft._dbh.db = event.target.result;
		// FNVCraft.log('Database update needed', 'notice');
	};
	
	request.onerror = FNVCraft._dbh.onError;
};

FNVCraft.start = function() {
	FNVCraft.log("Starting FNVCraft");
	
	var a_IndexedDB = '<a href="https://developer.mozilla.org/en-US/docs/Web/API/IndexedDB_API/Using_IndexedDB">indexedDB</a>';

	if (!window.indexedDB) {
		FNVCraft.log("Your browser doesn't support a stable version of "+a_IndexedDB, 'ferr');
		return;
	}
	
	FNVCraft.log("Good ! Your browser support a stable version of "+a_IndexedDB);
	
	FNVCraft._dbh.open();
};



jQuery( document ).ready(function() {

	// In the following line, you should include the prefixes of implementations you want to test.
	window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
	// DON'T use "var indexedDB = ..." if you're not in a function.
	// Moreover, you may need references to some window.IDB* objects:
	window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction;
	window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;
	// (Mozilla has never prefixed these objects, so we don't need window.mozIDB*)

	
	FNVCraft.start();
});

