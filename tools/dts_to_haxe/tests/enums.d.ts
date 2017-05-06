/**
 * MyJsDoc1
 */
export enum MOUSE { 
    LEFT,
    MIDDLE,
    
    /**
     * MyJsDoc for enum member
     */
    RIGHT
}

/**
 * MyJsDoc2
 */
export enum CullFace { }

/**
 * MyJsDoc3
 */
export const CullFaceNone: CullFace;
export const CullFaceBack: CullFace;
export const CullFaceFront: CullFace;
export const CullFaceFrontBack: CullFace;

/**
 * MyJsDoc4
 */
export enum FrontFaceDirection { }
export const FrontFaceDirectionCW: FrontFaceDirection;
export const FrontFaceDirectionCCW: FrontFaceDirection;
