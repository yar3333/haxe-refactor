/// <reference path="../typings/globals/node/index.d.ts" />

export class TsToHaxeStdTypes
{
    static getAll() : Map<string, string>
    {
        return new Map<string, string>
        ([
            [ "any", "Dynamic" ],
            [ "void", "Void" ],
            [ "string", "String" ],
            [ "number", "Float" ],
        ]);
    };
}