import { Scene, MyIden2 } from "./three-core";

export class PropertyBinding {
    testfunc(arr:number[], offset?:number) : void;
    
    traverse(callback:(a:string)=>any) : void;

    constructor(rootNode: any, path: string, parsedPath?: any);

    path: string;
    parsedPath: any;
    node: any;
    rootNode: any;

    getValue(targetArray: any, offset: number): any;
    setValue(sourceArray: any, offset: number): void;
    bind(): void;
    unbind(): void;

    BindingType: { [bindingType: string]: number };
    Versioning: { [versioning: string]: number };

    GetterByBindingType: Function[];
    SetterByBindingTypeAndVersioning: Array<Function[]>;

    static create(root: any, path: any, parsedPath?: any): PropertyBinding|PropertyBinding.Composite;
    static parseTrackName(trackName: string): any;
    static findNode(root: any, nodeName: string): any;
}

export namespace PropertyBinding {
    export class Composite {
        constructor(targetGroup: any, path: any, parsedPath?: any);

        getValue(array: any, offset: number): any;
        setValue(array: any, offset: number): void;
        bind(): void;
        unbind(): void;
    }
}

export class Curve<T extends Vector> {
    handlers: (RegExp | Loader)[];
    width : number;
    height: string;
    arr: Array<number>;
    arr2: number[];

    test(width:number, height:string) : void;
}

export interface ColladaLoaderReturnType extends BaseInt, Abc {
    dispatchEvent(event: { type: string; [attachment: string]: any; }): void;
    info: {render: {vertices: number; faces: number;};};
    pointMap: { [id: string]: number[]; };
    myfield: number|string;
    myfunc(a:number, b) : void;
}

/**
 * My_JS_Doc
 * And Second line
 */
export class ColladaLoader extends BaseInt implements Abc {
    animations: any[];
    
    /**
     * My_JS_Doc
     * And Second line
     */
    kinematics: any;
    scene: Scene;
    
    /**
     * My_JS_Doc
     * And Second line
     */
    constructor(a?:number);

    load(url: string, onLoad: (model: ColladaModel) => void, onProgress?: (request: ProgressEvent) => void): void;
    setCrossOrigin(value: any): void;

    /**
     * My_JS_Doc
     * And Second line
     */
    parse(text: string): ColladaModel;
}

class BaseInt {}
interface Abc {}
