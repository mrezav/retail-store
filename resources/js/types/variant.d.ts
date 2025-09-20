import { ProductResource } from "./product"

export interface VariantForm{
    id: number | null,
    product_id: number | null,
    merk:string,
    unit: string,
    color: string,
    dimension: string,
    stock: number,
    price: decimal
}

export interface VariantResource{
    id: number | null,
    product_id: number | null,
    merk:string,
    color: string |null,
    dimension: string,
    image_path: string |null,
    unit: string,
    stock: number,
    price: decimal,
    product: ProductResource
}