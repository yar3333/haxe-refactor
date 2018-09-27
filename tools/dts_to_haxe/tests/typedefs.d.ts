declare module MyTypedefs {
    type Nullable<T> = T | null;
    type float = number;
    type double = number;
    type int = number;
    type FloatArray = number[] | Float32Array;
    type IndicesArray = number[] | Int32Array | Uint32Array | Uint16Array;
    /**
     * Alias for types that can be used by a Buffer or VertexBuffer.
     */
    type DataArray = number[] | ArrayBuffer | ArrayBufferView;
} 