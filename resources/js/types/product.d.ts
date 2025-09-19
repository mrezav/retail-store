import { CategoryResource } from "./category";
import { VariantForm, VariantResource } from "./variant";

export interface ProductForm{
    id: number | null;
    name: string;
    slug: string;
    category_id: number | null;
    description: string;
    image: File | null;
    image_preview: string | null;
    image_path: string | null;
    is_active: boolean;
    variants: VariantForm[];
    _method:string;
}

export interface ProductResource {
    id: number;
    name: string;
    description: string;
    image: File | null;
    image_preview: string | null;
    image_path: string;
    merk: string;
    stock: number;
    price: decimal; 
    color: string;
    size: string;
    category_id: number;
    category: CategoryResource;
    variants: VariantResource[];
    created_at: string;
    updated_at: string;
    price_min: decimal|null;
    price_max: decimal|null;
}

