import { Scene, MyIden2 } from "./three-core";

export interface ColladaLoaderReturnType extends BaseInt, Abc {
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
    constructor();

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
