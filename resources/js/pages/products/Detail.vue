<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ProductResource } from '@/types/product';
import { Head } from '@inertiajs/vue3';
import { formatCurrency, formatDateTime } from '@/utils/helper';
import {route} from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Info Barang',
        href: '/products',
    },
];

const { product } = defineProps<{ product: ProductResource }>()

const totalStock = computed(()=>{
    return product.variants.map(variant => variant.stock).reduce((sum, stock) => sum + stock, 0)
})

const cheapest = computed(() => {
    return Math.min(...product.variants.map(p => Number(p.price)))
})
const expenses = computed(() => {
    return Math.max(...product.variants.map(p => Number(p.price)))
})

// const expenses = computed(() => {
//     return product.variants.reduce((min, item) =>
//     item.price > min.price ? item : min)
// })

</script>

<template>

    <Head title="Detail" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="w-9/10 p-6 m-auto">
                <div class="grid md:grid-cols-3 xl:md:grid-cols-3 gap-4 my-2">
                    <div class="box-content md:box-border rounded-lg bg-white shadow-lg shadow-blue-500/10">
                        <img :src="product.image_path" alt="image preview" class="aspect-4/3 object-fit">
                    </div>
                    <div class="grid col-span-2 justify-items-start">
                        <div class="overflow-x-auto">
                            <table class="table border-separate border-spacing-x-2 w-full">
                                <tbody>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60 w-32">Nama</th>
                                        <td>{{ product.name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Kategori</th>
                                        <td>
                                            <Link :href="route('categories.show', {id: product.category_id})" class="text-green-500 cursor-pointer hover:underline">
                                                {{ product.category.name }}    
                                            </Link>
                                        </td> 
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Jumlah Varian</th>
                                        <td>{{ product.variants.length }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Total stok</th>
                                        <td>{{ totalStock }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Harga</th>
                                        <td class="text-red-500">{{ formatCurrency(cheapest) }} <span v-if="expenses > cheapest"> ~ {{ formatCurrency(expenses) }} </span></td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Tanggal Input</th>
                                        <td>{{ formatDateTime(product.created_at) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Deskripsi</th>
                                        <td>{{ product.description }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div tabindex="0" class="collapse collapse-arrow bg-base-100 border-blue-300 border">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title font-semibold text-blue-500 ">Daftar Varian Barang</div>
                    <div class="collapse-content text-sm">
                        <div class="grid grid-flow-col grid-cols-4 justify-items-start gap-4">
                        <template v-for="variant in product.variants" :key="variant.id">
                            <div class="box-content w-full border md:box-border bg-white shadow-lg shadow-gray-sm p-2">
                                <table class="table table-zebra table-sm">
                                    <tbody>
                                        <tr>
                                            <th class="font-semibold opacity-60">Merk/Jenis</th>
                                            <td>{{ variant.merk }}</td>
                                        </tr>
                                        <tr>
                                            <th class="font-semibold opacity-60">Warna</th>
                                            <td>{{ variant.color }}</td>
                                        </tr>
                                        <tr>
                                            <th class="font-semibold opacity-60">Dimensi</th>
                                            <td>{{ variant.dimension }}</td>
                                        </tr>
                                        <tr>
                                            <th class="font-semibold opacity-60">Stok</th>
                                            <td>{{ variant.stock }} {{ variant.unit }}</td>
                                        </tr>
                                        <tr>
                                            <th class="font-semibold opacity-60">Harga</th>
                                            <td class="text-red-400 font-bold">{{ formatCurrency(variant.price) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
