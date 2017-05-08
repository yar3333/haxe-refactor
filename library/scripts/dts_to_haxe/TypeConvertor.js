"use strict";
const TsToHaxeStdTypes_1 = require("./TsToHaxeStdTypes");
class TypeConvertor {
    /**
     * Keys:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFuncOrVar` - return type of the function or variable type
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     *  `.a` - type of the all parameters "a"
     *  `@myFuncOrVar` - return type of the all functions or variables
     *  `fromType` - specified type in any place
     */
    constructor(custom) {
        this.mapper = TsToHaxeStdTypes_1.TsToHaxeStdTypes.getAll();
        for (let k of custom.keys()) {
            this.mapper.set(k, custom.get(k));
        }
    }
    /**
     * localePath:
     *  `mypack.MyClas<T` - type of class <TypeParameter>
     *  `mypack.MyClas@myFuncOrVar` - return type of the function or variable type
     *  `mypack.MyClas@myFunc.a` - type of the parameter "a"
     */
    convert(type, localePath) {
        if (localePath) {
            if (localePath.startsWith("@"))
                localePath = "." + localePath.substring(1);
            if (this.mapper.has(localePath))
                return this.mapper.get(localePath);
            if (localePath.indexOf("<") < 0) {
                var m = localePath.indexOf("@");
                if (m >= 0) {
                    if (this.mapper.has(localePath.substring(m)))
                        return this.mapper.get(localePath.substring(m));
                    var n = localePath.lastIndexOf(".");
                    if (n > m && this.mapper.has(localePath.substring(n)))
                        return this.mapper.get(localePath.substring(n));
                }
            }
        }
        return this.mapper.has(type) ? this.mapper.get(type) : type;
    }
}
exports.TypeConvertor = TypeConvertor;
//# sourceMappingURL=TypeConvertor.js.map