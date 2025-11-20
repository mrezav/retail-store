import TransactionDetailForm, { TransactionDetailResource } from "./transaction_detail";

export interface TransactionForm {
    id:number|null;
    user_id:number|null;
    buyer_id:number|null;
    buyer_name:string;
    total_price:number;
    additional_cost:number;
    discount:number;
    grand_total:number;
    is_installment:boolean;
    due_date:string|null;
    transaction_date:string;
    transaction_detail:TransactionDetailForm[];
    payment_nominal: number;
    _method:string;
}

export interface InstallmentPaymentResource{
    id: number|null,
    sequenct: number,
    payment_date: string,
    amount: number,
}

export interface TransactionResource{
    id:number|null;
    invoice_code:string;
    user_id:number|null;
    buyer_id:number|null;
    buyer_name:string;
    total_price:number;
    additional_cost:number;
    discount:number;
    grand_total:number;
    is_installment:boolean;
    due_date:string|null;
    transaction_date:string;
    transaction_details:TransactionDetailResource[];
    installment_payments:InstallmentPaymentResource[];
    is_done:boolean;
    created_at:string;
    updated_at:string;
}