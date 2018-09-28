package js.myTypedefs;

/**
 * Alias for types that can be used by a Buffer or VertexBuffer.
 */
typedef DataArray = haxe.extern.EitherType<Array<Float>, haxe.extern.EitherType<ArrayBuffer, ArrayBufferView>>;