<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { ProductResource } from '@/types/product';
import { Head } from '@inertiajs/vue3';
import { CategoryResource } from '@/types/category';
import List from '../products/List.vue';
import { Links, Meta } from '@/types/pagination';
import { formatDateTime } from '@/utils/helper';
import ModalZoomImage from '@/components/custom/ModalZoomImage.vue';
import { ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Info Kategori',
        href: '/categories',
    },
];

interface Response {
    data: ProductResource[],
    links: Links,
    meta: Meta,
}

const { category, products } = defineProps<{ category: CategoryResource, products: Response }>()

const isZoom = ref<boolean>(false)
const sourceImage = ref<string|null>()
function imageZoom(source: string|null){
    isZoom.value = true
    sourceImage.value = source
}
</script>

<template>

    <Head title="Category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="w-9/10 mt-4 mb-6">
                <div class="grid grid-cols-3 gap-4 my-2">
                    <div class="box-content md:box-border w-full rounded-lg bg-white shadow-lg shadow-blue-500/10">
                        <template v-if="category.image_path">
                            <img :src="category.image_path" @click="imageZoom(category.image_path)" alt="image preview" class="w-auto h-auto max-w-full mx-auto my-auto cursor-pointer">
                        </template>
                        <template v-else>
                            <div class="flex items-center justify-center h-48">
                                <span class="text-gray-400 italic">No Image Available</span>
                            </div>
                        </template>
                        <!-- <img :src="category.image_path" @click="imageZoom(category.image_path)" alt="image preview" class="w-auto h-auto max-w-full mx-auto my-auto cursor-pointer"> -->
                    </div>
                    <div class="grid col-span-2 justify-items-start">
                        <div class="overflow-x-auto">
                            <table class="table border-separate border-spacing-x-4 w-full">
                                <tbody>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60 w-32">Nama</th>
                                        <td>{{ category.name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60 w-32">Keterangan</th>
                                        <td>{{ category.description }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60 w-32">Total Barang</th>
                                        <td>{{ products.data.length }}</td>
                                    </tr>
                                    <tr>
                                        <th class="size-4xl font-semibold opacity-60 w-32">Dibuat</th>
                                        <td>{{ formatDateTime(category.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <ModalZoomImage :is-zoom="isZoom" @close-modal="isZoom=false">
                    <img :src="sourceImage??undefined"  alt="gambar"  class="object-contain rounded-lg"/>
                </ModalZoomImage>
                <List :products="products"></List>
            </div>
        </div>
    </AppLayout>
</template>
