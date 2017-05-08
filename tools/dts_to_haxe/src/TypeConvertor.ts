import * as ts from "typescript";
import { TsToHaxeStdTypes } from "./TsToHaxeStdTypes";

export class TypeConvertor
{
    private mapper : Map<string, string>;
    
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
        this.mapper = TsToHaxeStdTypes.getAll();
        for (let k of custom.keys())
        {
            this.mapper.set(k, custom.get(k));
        }
    }

    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFuncOrVar` - return type of the function or variable type
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    convert(type:string, localePath:string) : string
    {
        if (localePath)
        {
            if (localePath.startsWith("@")) localePath = "." + localePath.substring(1); // literal (anonimous) types

            if (this.mapper.has(localePath))
            {
                let r = this.testIf(type, this.mapper.get(localePath));
                if (r) return r;
            }

            if (this.mapper.has(localePath.replace("@", "*")))
            {
                let r = this.testIf(type, this.mapper.get(localePath.replace("@", "*")));
                if (r) return r;
            }

            if (localePath.indexOf("<") < 0)
            {
                var m = localePath.indexOf("@");
                
                if (m >= 0)
                {
                    if (this.mapper.has(localePath.substring(m)))
                    {
                        let r = this.testIf(type, this.mapper.get(localePath.substring(m)));
                        if (r) return r;
                    }
                    if (this.mapper.has("*" + localePath.substring(m + 1)))
                    {
                        let r = this.testIf(type, this.mapper.get("*" + localePath.substring(m + 1)));
                        if (r) return r;
                    }
                    var n = localePath.lastIndexOf(".");
                    if (n > m)
                    {
                        if (this.mapper.has(localePath.substring(n)))
                        {
                            let r = this.testIf(type, this.mapper.get(localePath.substring(n)));
                            if (r) return r;
                        }
                        if (this.mapper.has("*" + localePath.substring(n + 1)))
                        {
                            let r = this.testIf(type, this.mapper.get("*" + localePath.substring(n + 1)));
                            if (r) return r;
                        }
                    }
                }
            }
        }
        return this.mapper.has(type) ? this.mapper.get(type) : type;
    }

    private testIf(sourceType, resultType:string) : string
    {
        var match = /^(.+?)\s+if\s+(.+)$/.exec(resultType);
        if (!match) return resultType;
        return match[2] === sourceType ? match[1] : null
    }
}