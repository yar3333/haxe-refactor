package js.myTypedefs;

typedef IndicesArray = haxe.extern.EitherType<Array<Float>, haxe.extern.EitherType<Int32Array, haxe.extern.EitherType<Uint32Array, Uint16Array>>>;