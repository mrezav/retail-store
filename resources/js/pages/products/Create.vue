<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ProductForm } from '@/types/product';
import { route } from 'ziggy-js';
import { Loader2 } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { ref, onBeforeMount } from 'vue';
import ModalVariant from '@/pages/variants/ModalForm.vue'
import { VariantForm } from '../../types/variant';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products/create',
    }
]

interface Category {
    id: number | null,
    name: string
}

const { categories } = defineProps<{
    categories: Category[],
}>()
const localCategories = ref<Category[]>([...categories])

onBeforeMount(() => {
    localCategories.value.unshift({ id: null, name: 'Pilih kategori' })
})

const variants = ref<VariantForm[]>([])
const variantData = ref<VariantForm | null>(null)
const variantIndex = ref<number | null>(null)

const form = useForm<ProductForm>({
    id: null,
    name: '',
    slug: '',
    category_id: null,
    description: '',
    is_active: true,
    image: null,
    image_preview: null,
    image_path: null,
    variants: variants.value,
    _method: 'post'
});

function handleFile(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image = target.files[0];
        form.image_preview = URL.createObjectURL(form.image);
    }
}

function submitForm() {
    form.post(route('products.store'), {
        onError: (err) => {
            console.log(err)
        },
        preserveScroll: true,
    })
}

const showModal = ref<boolean>(false)

const addVariant = (e: VariantForm) => {
    console.log('add variant', e)
    form.variants.unshift(e)
    showModal.value = false
}

const editVariant = (e: VariantForm, i: number | null) => {
    console.log('edit variant', i)
    console.log('edit variant', e)

    if (i !== null) {
        form.variants[i] = {
            id: e.id,
            product_id: e.product_id,
            merk: e.merk,
            unit: e.unit,
            color: e.color,
            dimension: e.dimension,
            stock: e.stock,
            price: e.price,
            description: e.description,
            image: e.image,
            image_path:e.image_path,
            image_preview:e.image_preview,
            _method:e._method
        }
    }


    showModal.value = false
    variantData.value = null
    variantIndex.value = null
}

function handleEditVariant(param: VariantForm, i: number) {
    variantData.value = param
    variantIndex.value = i
    showModal.value = true
}

</script>

<template>
    <Head title="Create product" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="lg:w-4xl px-6 py-6">
                <form @submit.prevent="submitForm" class="space-y-8 divide-y divide-gray-900/10">
                    <div class="grid gap-4 border-b border-gray-900/10 pb-12">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="grid col-span-2">
                                <label for="name" class="block text-sm/6 font-medium text-gray-600">Nama Barang <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input v-model="form.name" type="text" name="name" id="name" placeholder="Nama Barang"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.name }"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.name">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="category" class="block text-sm/6 font-medium text-gray-600">Kategori <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <select v-model="form.category_id"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.category_id }"
                                        class="select select-primary text-gray-900 max-h-9 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 outline-gray-300 placeholder:text-gray-300 sm:text-sm/6">
                                        <template v-for="category, i in localCategories" :key="i">
                                            <option :value="category.id" :disabled="!category.id">{{ category.name }}
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.category_id">{{
                                    form.errors.category_id }}</p>
                            </div>
                        </div>
                        <div>
                            <label for="image" class="block text-sm/6 font-medium text-gray-600">Gambar</label>
                            <input type="file" @change="handleFile" name="image" id="image"
                                :class="{ 'outline-red-300 focus:outline-red-400': form.errors.image }"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6" />
                            <img v-if="form.image_preview" :src="form.image_preview" alt="Image Preview"
                                class="mt-4 h-24 w-48 object-cover rounded" />
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
                        <div>
                            <button class="btn btn-outline btn-accent" @click.prevent="showModal = true">Tambah Varian</button>
                            <p class="mt-2 text-sm/6 text-red-600 ">{{ form.errors.variants }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2" v-if="form.variants.length > 0">
                        <div v-for="(value, i) in form.variants" :key="i">
                            <button class="btn btn-primary" @click.prevent="handleEditVariant(value, i)">
                                {{ value.merk }} {{ value.color }} <div class="badge badge-sm badge-accent px-2"> Rp {{
                                    value.price }}
                                </div>
                            </button>
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
                        <Link :href="route('products.index')">
                            <Button class="rounded bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300 cursor-pointer">Kembali</Button>
                        </Link>
                    </div>
                </form>
            </div>
        </div>
        <ModalVariant :variantData="variantData" :variantIndex="variantIndex" :showModal="showModal"
            @close="showModal = false" @edit-variant="editVariant" @add-variant="addVariant" />

    </AppLayout>
</template>

<style lang="scss" scoped></style>