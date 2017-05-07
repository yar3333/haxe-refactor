/// <reference path="../typings/globals/node/index.d.ts" />
"use strict";
const fs = require("fs");
const path = require("path");
const ts = require("typescript");
const DtsFileParser_1 = require("./DtsFileParser");
const CmdOptions_1 = require("./CmdOptions");
const Logger_1 = require("./Logger");
const FsTools = require("./FsTools");
var options = new CmdOptions_1.CmdOptions();
options.add("target", "ES5", ["--target"], "ES3, ES5, ES6, ES2015 or Latest. Default is ES5.");
options.add("outDir", "hxclasses", ["--out-dir"], "Output directory. Default is 'hxclasses'.");
options.add("rootPackage", "", ["--root-package"], "Root package for generated classes. Default is empty.");
options.add("nativeNamespace", "", ["--native-namespace"], "Prefix package for @:native meta.");
options.add("logLevel", "warn", ["--log-level"], "Verbose level: 'none', 'warn' or 'debug'. Default is 'warn'.");
options.addRepeatable("imports", ["--import"], "Add import for each generated file.");
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
    default:
        console.log("Unknow target.");
        process.exit(1);
}
let filePaths = params.get("filePaths");
for (var i = 0; i < filePaths.length; i++) {
    if (fs.statSync(filePaths[i]).isDirectory()) {
        var allFiles = [];
        FsTools.walkSync(filePaths[i], (start, dirs, files) => allFiles = allFiles.concat(files.filter(x => x.endsWith(".d.ts")).map(x => start + "/" + x)));
        var after = filePaths.slice(i + 1);
        filePaths = filePaths.slice(0, i).concat(allFiles).concat(after);
        i += allFiles.length - 1;
    }
}
const program = ts.createProgram(filePaths, compilerOptions);
const typeChecker = program.getTypeChecker();
var results = new Array();
for (let sourceFile of program.getSourceFiles()) {
    console.log("Process file " + sourceFile.path);
    var parser = new DtsFileParser_1.DtsFileParser(sourceFile, typeChecker, params.get("rootPackage"), params.get("nativeNamespace"));
    parser.parse(results, new Logger_1.Logger(params.get("logLevel")));
}
for (var klass of results) {
    klass.addImports(params.get("imports"));
    let destFilePath = params.get("outDir") + "/" + klass.fullClassName.split(".").join("/") + ".hx";
    console.log("\tSave file " + destFilePath);
    FsTools.mkdirp(path.dirname(destFilePath));
    fs.writeFileSync(destFilePath, klass.toString());
}
//# sourceMappingURL=DtsToHaxe.js.map