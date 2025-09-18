import { VariantForm } from '@/types/variant';
import { InertiaForm } from '@inertiajs/vue3';

export function validation(params: InertiaForm<VariantForm>): InertiaForm<VariantForm>{
    // reset errors data when call this function again
    params.hasErrors = false
    params.errors = {} 
    if(params.merk == ''){
        params.hasErrors = true
        const errors = {...params.errors, merk: 'Merk salah'}
        params.errors = errors
    }
    
    if(params.unit == ''){
        params.hasErrors = true
        const errors = {...params.errors, unit: 'Satuan salah'}
        params.errors = errors
    }
    
    if(params.stock <= 0){
        params.hasErrors = true
        const errors = {...params.errors, stock: 'Stok salah'}
        params.errors = errors
    }
    
    if(params.price <= 0){
        params.hasErrors = true
        const errors = {...params.errors, price: 'Harga salah'}
        params.errors = errors
    }

    return params
}