<script setup lang="ts">
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { VariantResource } from '@/types/variant';
import { formatCurrency } from '@/utils/helper';
import { capitalize, ref } from 'vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Variant Detail',
        href: '/products',
    },
];
const { variant } = defineProps<{ variant: VariantResource }>()

const sourceImage = ref<string | null>()
const modalImageRef = ref<HTMLDialogElement>()
function imageZoom(source: string | null) {
    modalImageRef.value?.show()
    sourceImage.value = source
}
</script>

<template>
    <div>

        <Head title="Detail" />

        <AppLayout :breadcrumbs="breadcrumbs">
            <div class="flex flex-col items-center">
                <div class="w-9/10 p-6 m-auto">
                    <div class="grid md:grid-cols-3 xl:md:grid-cols-3 gap-4 my-2">
                        <div
                            class="grid items-center justify-center box-content md:box-border rounded-lg bg-white shadow-lg shadow-blue-500/10">
                            <img :src="variant.image_path" alt="image preview" @click="imageZoom(variant.image_path)"
                                class="w-auto h-auto max-w-full mx-auto my-auto cursor-pointer">
                        </div>
                        <div class="grid col-span-2 justify-items-start">
                            <div class="overflow-x-auto">
                                <table class="table border-separate border-spacing-x-2 w-full">
                                    <tbody>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60 w-32">Merk</th>
                                            <td class="text-blue-800 font-bold">{{ capitalize(variant.merk) }} ({{ variant.product.name }})</td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Warna</th>
                                            <td>
                                                {{ capitalize(variant.color??'') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Dimensi/Ukuran</th>
                                            <td>
                                                {{ capitalize(variant.dimension) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Total stok</th>
                                            <td>{{ variant.stock }} {{ variant.unit }}</td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Harga</th>
                                            <td class="text-red-500">{{ formatCurrency(variant.price) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Tanggal Input</th>
                                            <td>{{ variant.created_at }}</td>
                                        </tr>
                                        <tr>
                                            <th class="size-4xl font-semibold opacity-60">Deskripsi</th>
                                            <td>{{ variant.description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <dialog id="zoom_image" ref="modalImageRef" class="modal">
                        <div class="modal-box w-auto max-w-none max-w-[60vw] max-h-[80vh] overflow-auto p-4">
                            <img :src="sourceImage ?? undefined" class="object-contain rounded-lg" />
                        </div>
                        <form method="dialog" class="modal-backdrop">
                            <button>close</button>
                        </form>
                    </dialog>
                </div>
            </div>
        </AppLayout>
    </div>
</template>

<style lang="css" scoped></style>