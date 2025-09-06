<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ProductForm } from '@/types/product';
import { route } from 'ziggy-js';
import { Loader2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products/create',
    }
]

const {categories} = defineProps<{categories: object}>()
console.log(categories);

const form = useForm<ProductForm>({
    name: '',
    slug: '',
    categoryId: null,
    description: '',
    price: 0,
    stock: 0,
    isActive: true,
    merk: '',
    color: '',
    size: '',
    image: null,
    imagePreview: null,
});

function handleFile(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image = target.files[0];
        form.imagePreview = URL.createObjectURL(form.image);
    }
}

function submitForm() {
    form.post(route('products.store'), {
        preserveScroll: true,
    })
}

</script>

<template>

    <Head title="Create product" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="lg:w-4xl px-6 py-6">
                <form @submit.prevent="submitForm" class="space-y-8 divide-y divide-gray-900/10">
                    <div class="grid gap-4 border-b border-gray-900/10 pb-12">
                        <div>
                            <label for="name" class="block text-sm/6 font-medium text-gray-600">Nama
                                Barang</label>
                            <div class="mt-2">
                                <input v-model="form.name" type="text" name="name" id="name" placeholder="Nama Barang"
                                    :class="{ 'outline-red-300 focus:outline-red-400': form.errors.name }"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <p class="mt-2 text-sm/6 text-red-600" v-if="form.errors.name">{{ form.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Merk</label>
                                <input type="text" v-model="form.merk" placeholder="Masukkan merk"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Warna</label>
                                <input type="text" v-model="form.color" placeholder="Masukkan warna"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <div>
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Ukuran</label>
                                <input type="text" v-model="form.size" placeholder="5cm x 5cm"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div >
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Kategori</label>
                                <!-- <input type="number" placeholder="0" step="1" name="stock" v-model="form.stock"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"> -->
                            </div>
                            <div >
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Stok</label>
                                <input type="number" placeholder="0" step="1" name="stock" v-model="form.stock"
                                    class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <div class="col-span-2">
                                <label for="merk" class="block text-sm/6 font-medium text-gray-600">Harga</label>
                                <div
                                    class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                                    <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6">Rp</div>
                                    <input id="price" type="number" step="100" name="price" placeholder="0.000"
                                        v-model="form.price"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.price }"
                                        class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" />

                                </div>
                                <!-- <p>{{ formattedValue }}</p> -->
                                <!-- <input type="text" class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"> -->
                            </div>
                        </div>

                        <div>
                            <label for="image" class="block text-sm/6 font-medium text-gray-600">Gambar</label>
                            <input type="file" @change="handleFile" name="image" id="image"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6" />
                            <img v-if="form.imagePreview" :src="form.imagePreview" alt="Image Preview"
                                class="mt-4 h-24 w-48 object-cover rounded mask mask-heart" />
                            <p class="mt-2 text-sm/6 text-red-600" v-if="form.errors.image">{{ form.errors.image }}
                            </p>
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>

                        <div>
                            <label for="description" class="block text-sm/6 font-medium text-gray-600">Deskripsi</label>
                            <textarea v-model="form.description" id="description" name="description" rows="3"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"
                                placeholder="Deskripsi singkat tentang barang ini..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-center gap-4">
                        <Button :disabled="form.processing" type="submit"
                            class="rounded bg-blue-500 px-4 py-2 font-semibold text-white hover:bg-blue-600 cursor-pointer">
                            <template v-if="!form.processing">Save</template>
                            <template v-else>
                                <Loader2 class="w-4 h-4 mr-1 animate-spin" />
                                Saving...
                            </template>
                        </Button>
                        <Link :href="route('categories.index')">
                        <Button
                            class="rounded bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300 cursor-pointer">Cancel</Button>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<style lang="scss" scoped></style>