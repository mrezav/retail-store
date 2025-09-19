<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Check, OctagonAlert, Pencil, ReceiptText, Trash } from 'lucide-vue-next';
import { Plus } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import CustomPaginate from '@/components/custom/CustomPaginate.vue';
import ModalConfirm from '@/components/custom/ModalConfirm.vue';
import { CategoryResource } from '@/types/category';
import { Links, Meta } from '@/types/pagination';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Kategori',
        href: '/categories',
    },
];

const pageData = usePage()
const showMessage = ref<boolean>(false);
const message = ref<string>('');
const flash = (pageData.props as { flash?: { message?: string } }).flash;
if (flash && flash.message) {
    showMessage.value = true;
    message.value = flash.message;
    setTimeout(() => {
        showMessage.value = false;
    }, 3000);
}


interface Response {
    data: CategoryResource[],
    links: Links
    meta: Meta;
}
const { categories } = defineProps<{ categories: Response }>();
console.log(categories);

const modalData = reactive({
    showModal: false,
    targetUrl: '',
    categoryId: null as number | null,
    categoryName: '',
});

function handleModal(event: Event) {
    event.preventDefault();
    const target = event.currentTarget as HTMLAnchorElement;
    modalData.targetUrl = target.dataset.url || '';
    modalData.showModal = true;
    modalData.categoryId = Number(modalData.targetUrl.split('/')[2]);
    console.log('Target URL:', modalData.targetUrl);
}

</script>

<template>

    <Head title="Kategori" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2 pb-8">
            <div class="flex justify-between mx-6 mt-2 mb-6" >
                <div>
                    <Alert class="bg-green-50 max-w-250 border-green-200 text-green-700" v-if="showMessage">
                        <Check class="h-4 w-4" />
                        <AlertTitle>Success</AlertTitle>
                        <AlertDescription>
                            {{ message }}
                        </AlertDescription>
                    </Alert>
                </div>
                <div>
                    <Link :href="route('categories.create')">
                        <Button
                            class="bg-teal-400 hover:shadow-lg hover:shadow-teal-100 hover:bg-teal-500 cursor-pointer">
                            Kategori Baru 
                            <Plus></Plus>
                        </Button>
                    </Link>
                </div>
            </div>
            <div v-if="categories.data.length > 0"
                class="grid gap-6 mt-4 px-6 py-6 border-b border-gray-200 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">

                <div v-for="(category, i) in categories.data" :key="i" v-bind="$attrs" 
                    class="box-content md:box-border rounded-lg bg-white shadow-lg shadow-blue-500/10">
                    <div class="relative">
                        <div
                            class="absolute w-full h-full z-10 bg-black opacity-0 hover:opacity-80 transition flex items-center justify-center gap-8 rounded-lg">
                            <Link :href="route('categories.show', {id: category.id})" class="relative inline-block group">
                                <button class="text-blue-900 cursor-pointer">
                                    <ReceiptText></ReceiptText>
                                </button>
                                <div
                                    class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                    Lihat
                                </div>
                            </Link>
                            <Link :href="`/categories/${category.id}/edit`" class="relative inline-block group">
                                <button class="text-green-800 cursor-pointer">
                                    <Pencil></Pencil>
                                </button>
                                <div
                                class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                Ubah
                                </div>
                            </Link>
                            <a href="#" @click.prevent="handleModal" :data-url="`/categories/${category.id}`"  class="relative inline-block group">
                                <button class="text-red-800 cursor-pointer ">
                                    <Trash></Trash>
                                </button>
                                <div
                                    class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                    Hapus
                                </div>
                            </a>
                        </div>
                        <img :src="category.image_path" alt="" srcset="" class="h-56 w-full object-cover rounded-lg" />
                    </div>
                    <div class="grid grid-cols-1 justify-items-center p-2">
                        <h4 class="text-lg font-semibold text-gray-500">{{ category.name }}</h4>
                        <p class="mt-1 text-sm text-gray-600">{{ category.description }}</p>
                    </div>
                </div>
            </div>
            <CustomPaginate :pagination="categories.meta" />


            <ModalConfirm :modalData="modalData"  @close-modal="modalData.showModal = false">
                <div class="grid grid-flow-col grid-rows-1 items-center justify-center gap-2 mb-4">
                    <OctagonAlert></OctagonAlert>
                    <h2 class="font-bold text-xl">Konfirmasi</h2>
                </div>
                <p>
                    Yakin menghapus kategori ini?
                </p>
            </ModalConfirm>
            
        </div>
    </AppLayout>
</template>
