import { ProductResource } from "./product"

export interface VariantForm{
    id: number | null,
    product_id: number | null,
    merk:string,
    unit: string,
    color: string | null,
    dimension: string,
    stock: number,
    price: decimal,
    description: string | null,
    image: File | null;
    image_preview: string | null;
    image_path: string | null;
    _method:string | null;
}

export interface VariantResource{
    id: number | null,
    product_id: number | null,
    merk:string,
    color: string |null,
    dimension: string,
    unit: string,
    stock: number,
    price: decimal,
    product: ProductResource
    description: string | null;
    image: File | null;
    image_preview: string | null;
    image_path: string;
    created_at:string;
    updated_at:string;
    _method:string | null;
}