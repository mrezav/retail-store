import { ProductResource } from "./product";
import { VariantResource } from "./variant";

export default interface TransactionDetailForm {
    id: number|null;
    transaction_id: number|null;
    product_id: number;
    variant_id: number;
    product_name: string;
    variant_merk: string;
    variant_unit: string;
    variant_color: string | null;
    variant_dimension: string;
    quantity: number;
    price: number;
    sub_total: number;
    created_at: string;
    updated_at: string;
    _method: string | null;
}

export interface ProductHistoryResource{
    id: number | null,
    transaction_detail_id: number | null,
    name:string,
    merk:string,
    color: string |null,
    dimension: string,
    unit: string,
    stock: number,
    price: decimal,
    created_at:string;
}

export interface TransactionDetailResource {
    id: number|null;
    transaction_id: number|null;
    sequence: number;
    product_id: number;
    product: ProductResource;
    variant_id: number;
    variant: VariantResource;
    product_history:ProductHistoryResource;
    quantity: number;
    price: number;
    sub_total: number;
    created_at: string;
    updated_at: string;
}