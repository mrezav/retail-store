<script setup lang="ts">
import CustomPaginate from '@/components/custom/CustomPaginate.vue';
import { Links, Meta } from '@/types/pagination';
import { ProductResource } from '@/types/product';
import { ref } from 'vue';
import ModalZoomImage from '@/components/custom/ModalZoomImage.vue';

interface Response {
    data: ProductResource[],
    links: Links,
    meta: Meta,
}

const { products } = defineProps<{
    products: Response
}>()

const isZoom = ref<boolean>(false)
const sourceImage = ref<string|null>()
function imageZoom(source: string|null){
    isZoom.value = true
    sourceImage.value = source
}
</script>

<template>
    <div class="overflow-x-auto mt-4 divide-y divide-gray-900/10">
        <table class="table bg-gray-50">
            <!-- head -->
            <thead>
                <tr>
                    <th>#</th>
                    <th class="text-center">Gambar</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th class="w-2/6">Varian</th>
                </tr>
            </thead>
            <tbody>
                <!-- row 1 -->
                <tr v-if="products.data.length < 1" class="text-center">
                    <th colspan="5">Tidak ada barang di kategori ini</th>
                </tr>
                <tr v-for="(product, i) in products.data" :key="i">
                    <th>{{ i + 1 }}</th>
                    <td>
                        <div class="flex items-center justify-center gap-3">
                            <div class="avatar">
                                <div class="h-24 w-28">
                                    <img :src="product.image_path" @click="imageZoom(product.image_path)" alt="image preview" 
                                        class="w-auto h-auto max-w-full mx-auto my-auto cursor-pointer" />
                                </div>
                            </div>

                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="text-lg">{{ product.name }}</div>
                        </div>
                    </td>
                    <td>
                        {{ product.description }}
                    </td>
                    <td>
                        <template v-for="variant in product.variants" :key="variant.id">
                            <div class="badge badge-accent p-2 m-2">{{ variant.merk }}</div>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
        <div v-if="products.data.length >= 1">
            <CustomPaginate :pagination="products.meta"></CustomPaginate>
        </div>
        <ModalZoomImage :is-zoom="isZoom" @close-modal="isZoom=false">
            <img :src="sourceImage??undefined"  alt="gambar" class="w-auto h-auto max-w-full mx-auto"/>
        </ModalZoomImage>
    </div>
</template>

<style lang="scss" scoped></style>