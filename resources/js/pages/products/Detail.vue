<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ProductResource } from '@/types/product';
import { Head, router } from '@inertiajs/vue3';
import { formatCurrency, formatDateTime } from '@/utils/helper';
import { route } from 'ziggy-js';
import { Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { VariantResource } from '@/types/variant';
import { Links, Meta } from '@/types/pagination';
import CustomPaginate from '@/components/custom/CustomPaginate.vue';
import { SearchIcon } from 'lucide-vue-next';
import { useDebounce } from '@vueuse/core';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Info Barang',
        href: '/products',
    },
];

const { product, variants, search } = defineProps<{ product: ProductResource, variants: Response, search: string | null }>()
const keyword = ref<string | null>(search)

interface Response {
    data: VariantResource[],
    links: Links,
    meta: Meta,
}


const totalStock = computed(() => {
    return variants.data.map(variant => variant.stock).reduce((sum, stock) => sum + stock, 0)
})

const cheapest = computed(() => {
    return Math.min(...variants.data.map(p => Number(p.price)))
})
const expenses = computed(() => {
    return Math.max(...variants.data.map(p => Number(p.price)))
})

const searchInput = ref<HTMLInputElement | null>(null)

// function inertiaGet(url: string, data: Record<string, any> = {}): Promise<any> {
//   return new Promise((resolve, reject) => {
//     router.get(url, data, {
//         onSuccess: (page) => resolve(page),
//         onError: (errors) => reject(errors),
//         preserveState: true,
//         preserveScroll:true,
//         replace:true
//     })
//   })
// }
// watch(keyword, async (newVal) => {
//   if (!newVal) return

//   try {
//     // panggil inertia router.get dalam bentuk promise
//     const page = await inertiaGet(route('products.show', {id: product.id}), { keyword: newVal })

//     // ambil data dari props
//     // products.value = page.props.products
//     console.log(page)

//     // setelah data masuk & DOM update → fokus input lagi
//     await nextTick()
//     searchInput.value?.focus()
//   } catch (err) {
//     console.error("Gagal fetch produk:", err)
//   }
// })




const debounce = useDebounce(keyword, 400);
watch(
    debounce,
    () => router.get(
        route('products.show', { id: product.id }),
        { keyword: keyword.value },
        {
            preserveScroll: true,
            preserveState:true,
            replace: true
        }
    ),
    { deep: true }
)

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
                                            <Link :href="route('categories.show', { id: product.category_id })"
                                                class="text-green-500 cursor-pointer hover:underline">
                                            {{ product.category.name }}
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Jumlah Varian</th>
                                        <td>{{ variants.data.length }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Total stok</th>
                                        <td>{{ totalStock }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60">Harga</th>
                                        <td class="text-red-500">{{ formatCurrency(cheapest) }} <span
                                                v-if="expenses > cheapest"> ~ {{ formatCurrency(expenses) }} </span>
                                        </td>
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
                <div class="grid grid-cols-3 mt-6 mb-2">
                    <div>
                        <h3 class="text-2xl text-gray-600 font-bold">Daftar Varian</h3>
                    </div>
                    <div class="grid col-span-2 justify-end">
                        <label class="input input-md input-accent border-2 border-green-200 mr-4">
                            <SearchIcon class="text-green-300 text-sm"></SearchIcon>
                            <input ref="searchInput" v-model="keyword" type="search" required placeholder="Search" />
                        </label>
                    </div>
                </div>
                {{ keyword }}
                <template v-if="variants.data.length > 0">
                <div class="grid grid-cols-4 justify-items-start gap-4">
                    <template v-for="variant in variants.data" :key="variant.id">
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
                <CustomPaginate :pagination="variants.meta"></CustomPaginate>
                </template>
                <div v-else>
                <div class="grid grid-rows-2 justify-center mt-18">
                    <div class="grid place-items-center">
                        <img src="/notfound.png" class="w-40 opacity-60">
                    </div>
                    <div class="grid item-center mt-8">
                        <h3 class="opacity-60 text-2xl font-bold">Varian tidak ditemukan!</h3>
                    </div>
                </div>
            </div>
                <!-- <div tabindex="0"  class="collapse collapse-arrow bg-base-100 border-blue-300 border">
                    <input type="checkbox" class="peer" />
                    <div class="collapse-title font-semibold text-blue-500 ">Daftar Varian Barang</div>
                    <div class="collapse-content text-sm w-full">
                        
                    </div>
                </div> -->

            </div>
        </div>
    </AppLayout>
</template>
