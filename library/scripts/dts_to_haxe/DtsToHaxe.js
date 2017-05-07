/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const fs = require("fs");
const path = require("path");
const ts = require("typescript");
const DtsFileParser_1 = require("./DtsFileParser");
const CmdOptions_1 = require("./CmdOptions");
var options = new CmdOptions_1.CmdOptions();
options.add("target", "ES5", ["--target"], "ES3, ES5, ES6, ES2015 or Latest. Default is ES5.");
options.add("outDir", "hxclasses", ["--out-dir"], "Output directory. Default is 'hxclasses'.");
options.add("rootPackage", "", ["--root-package"], "Root package for generated classes. Default is empty.");
options.addRepeatable("filePaths", null, "Source typescript definition file path or directory.");
if (process.argv.length <= 2) {
    console.log("TypeScript definition files (*.d.ts) to haxe convertor.");
    console.log("Usage: dts_to_haxe <options> <filePaths> ...");
    console.log("Options:");
    console.log(options.getHelpMessage());
    process.exit(1);
}
var params = options.parse(process.argv.slice(2));
const compilerOptions = {
    target: ts.ScriptTarget.ES5,
    module: ts.ModuleKind.CommonJS,
    noLib: true
};
switch (params.get("target").toUpperCase()) {
    case "ES3":
        compilerOptions.target = ts.ScriptTarget.ES3;
        break;
    case "ES5":
        compilerOptions.target = ts.ScriptTarget.ES5;
        break;
    case "ES6":
        compilerOptions.target = ts.ScriptTarget.ES6;
        break;
    case "ES2015":
        compilerOptions.target = ts.ScriptTarget.ES2015;
        break;
    case "LATEST":
        compilerOptions.target = ts.ScriptTarget.Latest;
        break;
    default: error("Unknow target.");
}
let filePaths = params.get("filePaths");
for (var i = 0; i < filePaths.length; i++) {
    if (fs.statSync(filePaths[i]).isDirectory()) {
        var allFiles = [];
        walkSync(filePaths[i], (start, dirs, files) => allFiles = allFiles.concat(files.filter(x => x.endsWith(".d.ts")).map(x => start + "/" + x)));
        var after = filePaths.slice(i + 1);
        filePaths = filePaths.slice(0, i).concat(allFiles).concat(after);
        i += allFiles.length - 1;
    }
}
const program = ts.createProgram(filePaths, compilerOptions);
const typeChecker = program.getTypeChecker();
for (let sourceFile of program.getSourceFiles()) {
    console.log("Process file " + sourceFile.path);
    var parser = new DtsFileParser_1.DtsFileParser(sourceFile, typeChecker, params.get("rootPackage"));
    for (var klass of parser.parse()) {
        let destFilePath = params.get("outDir") + "/" + klass.fullClassName.split(".").join("/") + ".hx";
        mkdirp(path.dirname(destFilePath));
        fs.writeFileSync(destFilePath, klass.toString());
    }
}
function error(s) {
    console.log(s);
    process.exit(1);
}
function walkSync(start, callback) {
    var stat = fs.statSync(start);
    if (stat.isDirectory()) {
        var filenames = fs.readdirSync(start);
        var coll = filenames.reduce(function (acc, name) {
            var abspath = path.join(start, name);
            if (fs.statSync(abspath).isDirectory()) {
                acc.dirs.push(name);
            }
            else {
                acc.names.push(name);
            }
            return acc;
        }, { "names": [], "dirs": [] });
        callback(start, coll.dirs, coll.names);
        coll.dirs.forEach(function (d) {
            var abspath = path.join(start, d);
            walkSync(abspath, callback);
        });
    }
    else {
        throw new Error("path: " + start + " is not a directory");
    }
}
function mkdirp(p, mode, made) {
    if (mode === undefined)
        mode = parseInt('0777', 8) & (~process.umask());
    if (!made)
        made = null;
    p = path.resolve(p);
    try {
        fs.mkdirSync(p, mode);
        made = made || p;
    }
    catch (err0) {
        switch (err0.code) {
            case 'ENOENT':
                made = mkdirp(path.dirname(p), mode, made);
                mkdirp(p, mode, made);
                break;
            // In the case of any other error, just see if there's a dir
            // there already.  If so, then hooray!  If not, then something
            // is borked.
            default:
                var stat;
                try {
                    stat = fs.statSync(p);
                }
                catch (err1) {
                    throw err0;
                }
                if (!stat.isDirectory())
                    throw err0;
                break;
        }
    }
    return made;
}
;
//# sourceMappingURL=DtsToHaxe.js.map