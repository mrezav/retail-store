<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { VariantForm, VariantResource } from '@/types/variant';
import { route } from 'ziggy-js';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Variant',
        href: '/variants',
    },
];

interface ProductType {
    id: null,
    name: string,
}

const { products, variant } = defineProps<{ products: ProductType[], variant: VariantResource }>()
const localProducts = ref<ProductType[]>([{ id: null, name: "Pilih Produk" }, ...products])
console.log(variant);
console.log(products);

const form = useForm<VariantForm>(
    {
        id: variant.id,
        product_id:variant.product_id,
        merk:variant.merk,
        unit:variant.unit,
        color:variant.color,
        dimension:variant.dimension,
        stock:variant.stock,
        price:variant.price,
        description:variant.description,
        image: null,
        image_path: variant.image_path,
        image_preview: null,
        _method: 'put',
    }
)

function submitForm() {
    if(form.id){
        form.post(route('variants.update', {id:form.id}), {
            forceFormData:true,
            onSuccess:() => {
                console.log('suceess')
            },
            onError:(e) => {
                console.log('failed >>', e)
            }
        })
    }else{
        console.log('no variant id')
    }
}

function handleFile(e:Event) {
    const target = e.target as HTMLInputElement
    if(target.files && target.files.length > 0){
        form.image = target.files[0]
        form.image_preview = URL.createObjectURL(form.image)
        form.image_path = null
    }
}
</script>

<template>

    <Head title="Variant" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="lg:w-4xl px-6 py-6">
                <form @submit.prevent="submitForm">
                    <div class="grid gap-6 border-b border-gray-900/10 pb-12">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="category" class="block text-sm/6 font-medium text-gray-600">Barang <span
                                        class="text-red-500">*</span></label>
                                <div>
                                    <select v-model="form.product_id"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.product_id }"
                                        class="select select-primary w-full text-gray-900 max-h-9 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 outline-gray-300 placeholder:text-gray-300 sm:text-sm/6">
                                        <template v-for="(product, i) in localProducts" :key="i">
                                            <option :value="product.id" :disabled="!product.id">{{ product.name }}
                                            </option>
                                        </template>
                                    </select>

                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.product_id">{{
                                    form.errors.product_id }}</p>
                            </div>
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Merk/Jenis <span
                                        class="text-red-500">*</span></label>
                                <input type="text" v-model="form.merk" placeholder="Masukkan merk atau jenis"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                <p v-if="form.errors.merk" class="text-sm text-red-500 mt-1">{{
                                    form.errors.merk }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Warna </label>
                                <input type="text" v-model="form.color" placeholder="Masukkan warna"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <div>
                                <label for="merk"
                                    class="block text-sm/6 font-medium text-gray-600">Dimensi/Ukuran</label>
                                <input type="text" v-model="form.dimension" placeholder="5cm x 5cm / S / M / L"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-4 mt-4">
                            <div class="col-span-2">
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Stok <span
                                        class="text-red-500">*</span></label>
                                <input type="number" placeholder="0" step="1" name="stock" v-model="form.stock"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                <p v-if="form.errors.stock" class="text-sm text-red-500 mt-1">{{
                                    form.errors.stock }}</p>
                            </div>
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Satuan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" v-model="form.unit" placeholder="Pcs/Cm/Kg/M&sup3;"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                <p v-if="form.errors.unit" class="text-sm text-red-500 mt-1">{{
                                    form.errors.unit }}</p>
                            </div>
                            <div class="col-span-3">
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Harga <span
                                        class="text-red-500">*</span></label>
                                <div
                                    class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                                    <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6">Rp</div>
                                    <input id="price" type="number" step="1" name="price" placeholder="0.000"
                                        v-model="form.price"
                                        class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" />
                                </div>
                                <p v-if="form.errors.price" class="text-sm text-red-500 mt-1">{{
                                    form.errors.price }}</p>
                            </div>
                        </div>
                        <div>
                           <label for="image" class="block text-sm/6 font-medium text-gray-600">Gambar</label>
                            <input type="file" @change="handleFile" name="image" id="image"
                                :class="{ 'outline-red-300 focus:outline-red-400': form.errors.image }"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6" />
                            <img v-if="form.image_preview" :src="form.image_preview" alt="Image Preview" class="mt-4 h-24 w-48 object-cover rounded" />
                            <img v-if="form.image_path" :src="form.image_path" alt="Image Preview" class="mt-4 h-24 w-48 object-cover rounded">
                            
                            <p class="mt-2 text-sm/6 text-red-600" v-if="form.errors.image">{{ form.errors.image }}</p>
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>
                        <div class="mt-4">
                            <label for="description" class="block text-sm/6 font-medium text-gray-600">Deskripsi</label>
                            <textarea v-model="form.description" id="description" name="description" rows="3"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"
                                placeholder="Deskripsi singkat tentang varian ini..."></textarea>

                        </div>
                    </div>

                    <div class="mt-4 flex justify-center gap-4">
                        <Button :disabled="form.processing" type="submit"
                            class="rounded bg-blue-500 px-4 py-2 font-semibold text-white hover:bg-blue-600 cursor-pointer">
                            <template v-if="!form.processing">Simpan</template>
                            <template v-else>
                                <Loader2 class="w-4 h-4 mr-1 animate-spin" />
                                Saving...
                            </template>
                        </Button>
                        <Link :href="route('variants.index')">
                        <Button
                            class="rounded bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300 cursor-pointer">Kembali</Button>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>