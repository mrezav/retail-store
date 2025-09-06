<script setup lang="ts">
import { BreadcrumbItem } from '@/types';
import { CategoryForm } from '@/types/category';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Button from '@/components/ui/button/Button.vue';
import { Loader2 } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categories',
        href: '/categories/{id}/edit',
    },
];

 
const {category} = defineProps<{category: {
    id: number,
    name: string,
    slug: string,
    description: string,
    image_path: string,
}}>();

console.log('Category >>>', category);

const form = useForm<CategoryForm>({
    name: category.name,
    description: category.description,
    image: null,
    imagePreview: category.image_path,
});

console.log('Form >>>', form);

function handleFile(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.image = target.files[0];
        form.imagePreview = URL.createObjectURL(form.image);
    } else {
        form.image = null;
    }
}

function submitForm() {
    form.post(route('categories.update', category.id), {
        preserveScroll: true,
    });
}

</script>

<template>
    <Head title="Create category" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="lg:w-4xl px-6 py-6">
                <form @submit.prevent="submitForm" class="space-y-8 divide-y divide-gray-900/10">
                    <div class="grid gap-4 border-b border-gray-900/10 pb-12">
                        <div>
                            <label for="category" class="block text-sm/6 font-medium text-gray-600">Nama
                                Kategori</label>
                            <div class="mt-2">
                                <input v-model="form.name" type="text" name="category" id="category" placeholder="Nama Kategori"
                                    :class="{ 'outline-red-300 focus:outline-red-400': form.errors.name }"
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            </div>
                            <p class="mt-2 text-sm/6 text-red-600" v-if="form.errors.name">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="image" class="block text-sm/6 font-medium text-gray-600">Gambar</label>
                            <input type="file" @change="handleFile" name="image" id="image"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6" />
                            <img v-if="form.imagePreview" :src="form.imagePreview" alt="Image Preview"
                                class="mt-4 h-24 w-48 object-cover rounded" />
                            <p class="mt-2 text-sm/6 text-red-600" v-if="form.errors.image">{{ form.errors.image }}</p>
                            <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                {{ form.progress.percentage }}%
                            </progress>
                        </div>

                        <div>
                            <label for="description" class="block text-sm/6 font-medium text-gray-600">Deskripsi</label>
                            <textarea v-model="form.description" id="description" name="description" rows="3"
                                class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"
                                placeholder="Deskripsi singkat tentang kategori ini..."></textarea>
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

<style lang="scss" scoped>

</style>