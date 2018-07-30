"use strict";
class TypePathTools {
    static normalizeFullClassName(s) {
        var pp = s.split(".");
        return pp.slice(0, pp.length - 1).map(TypePathTools.decapitalize).concat([TypePathTools.capitalize(pp[pp.length - 1])]).join(".");
    }
    static capitalize(s) {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }
    static decapitalize(s) {
        if (/^[_ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789]+$/.test(s))
            return s.toLowerCase();
        return s.charAt(0).toLowerCase() + s.slice(1);
    }
    static makeFullClassPath(parts) {
        var s = "";
        for (var p of parts) {
            if (p !== null && p !== "" && s != "")
                s += ".";
            s += p;
        }
        return s;
    }
    static splitFullClassName(fullClassName) {
        var packageName = '';
        var className = fullClassName;
        if (fullClassName.lastIndexOf('.') != -1) {
            packageName = fullClassName.substr(0, fullClassName.lastIndexOf('.'));
            className = fullClassName.substr(fullClassName.lastIndexOf('.') + 1);
        }
        return { packageName: packageName, className: className };
    }
}
exports.TypePathTools = TypePathTools;
//# sourceMappingURL=TypePathTools.js.map