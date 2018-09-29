import * as ts from "typescript";
import { TypePathTools } from "./TypePathTools"

export class TypeMapper
{
    private data : Map<string, string>;
    
    /**
     * Keys:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFuncOrVar` - return type of the function or variable type
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     *  `.a` - type of the all parameters "a"
     *  `@myFuncOrVar` - return type of the all functions or variables
     *  `fromType` - specified type in any place
     */
    constructor(custom:Map<string, string>)
    {
        this.data = new Map<string, string>
        ([
            [ "any", "Dynamic" ],
            [ "void", "Void" ],
            [ "string", "String" ],
            [ "number", "Float" ],
            [ "boolean", "Bool" ],
            [ "Boolean", "Bool" ],
            [ "Object", "{}" ],
            [ "Function", "haxe.Constraints.Function"],
            [ "true", "Bool"],
            [ "false", "Bool"],
            [ "Nullable", "Null"]
        ]);
        
        for (let k of custom.keys())
        {
            this.data.set(k, custom.get(k));
        }
    }

    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFuncOrVar` - return type of the function or variable type
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    map(type:string, localePath:string, knownTypes:Array<string>, curPack:string) : string
    {
        if (type == "this" && localePath && localePath.indexOf("@") > 0)
        {
            type = localePath.split("@")[0];
        }
        
        if (type.indexOf(".") >= 0)
        {
            var possibleKnownType = TypePathTools.normalizeFullClassName(TypePathTools.makeFullClassPath([ curPack, type ]));
            if (knownTypes.indexOf(possibleKnownType) >= 0) type = possibleKnownType;
        }

        type = this.data.has(type) ? this.getMapperValue(type, localePath) : type;

        if (localePath)
        {
            if (localePath.startsWith("@")) localePath = "." + localePath.substring(1); // literal (anonimous) types

            if (this.data.has(localePath))
            {
                let r = this.testIf(type, this.getMapperValue(localePath, localePath));
                if (r) return r;
            }

            if (this.data.has(localePath.replace("@", "*")))
            {
                let r = this.testIf(type, this.getMapperValue(localePath.replace("@", "*"), localePath));
                if (r) return r;
            }

            if (localePath.indexOf("<") < 0)
            {
                var m = localePath.indexOf("@");
                
                if (m >= 0)
                {
                    if (this.data.has(localePath.substring(m)))
                    {
                        let r = this.testIf(type, this.getMapperValue(localePath.substring(m), localePath));
                        if (r) return r;
                    }
                    if (this.data.has("*" + localePath.substring(m + 1)))
                    {
                        let r = this.testIf(type, this.getMapperValue("*" + localePath.substring(m + 1), localePath));
                        if (r) return r;
                    }
                    var n = localePath.lastIndexOf(".");
                    if (n > m)
                    {
                        if (this.data.has(localePath.substring(n)))
                        {
                            let r = this.testIf(type, this.getMapperValue(localePath.substring(n), localePath));
                            if (r) return r;
                        }
                        if (this.data.has("*" + localePath.substring(n + 1)))
                        {
                            let r = this.testIf(type, this.getMapperValue("*" + localePath.substring(n + 1), localePath));
                            if (r) return r;
                        }
                    }
                }
            }
        }
        
        if (type == "self" && localePath.indexOf("@") > 0)
        {
            return localePath.split("@")[0];
        }
        
        return type;
    }

    private testIf(sourceType, resultType:string) : string
    {
        var match = /^(.+?)\s+if\s+(.+)$/.exec(resultType);
        if (!match) return resultType;
        return match[2] === sourceType ? match[1] : null
    }

    private getMapperValue(key:string, localePath:string) : string
    {
        var v = this.data.get(key);
        if (v && localePath && localePath.indexOf("@") > 0) v = v.replace(/\bself\b/g, localePath.split("@")[0]);
        return v;
    }
}