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

export function variantDefaultForm(method: string = ''): VariantForm{
    return {
        id: null,
        product_id: null,
        merk: '',
        unit: '',
        color: '',
        dimension: '',
        stock: 0,
        price: 0,
        description:'',
        image: null,
        image_path: '',
        image_preview: null,
        _method: method ?? null,
    }
}