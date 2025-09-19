<script setup lang="ts">
import { BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Links } from '@/types/pagination';
import { Meta } from '../../types/pagination';
import { ProductResource } from '@/types/product';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Check, Pencil, Trash, OctagonAlert, ReceiptText } from 'lucide-vue-next';
import { Plus, SearchIcon } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed, onBeforeMount, reactive, ref, watch } from 'vue';
import CustomPaginate from '@/components/custom/CustomPaginate.vue';
import ModalConfirm from '@/components/custom/ModalConfirm.vue';
import { useDebounce } from '@vueuse/core';
import { formatCurrency } from '@/utils/helper';



const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Barang',
        href: '/products',
    },
];

interface FilterType{
    keyword:string, 
    category_id:number|null
}
interface Response {
    data: ProductResource[],
    links: Links,
    meta: Meta,
}

const { products, categories, search } = defineProps<{ products: Response, categories: {id: number|null, name: string}[], search: FilterType | null }>()
console.log(products);
console.log(categories);
console.log(search);


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

const modalData = reactive({
    showModal: false,
    targetUrl: '',
});
function handleModal(event: Event) {
    event.preventDefault();
    const target = event.currentTarget as HTMLAnchorElement;
    modalData.targetUrl = target.dataset.url || '';
    modalData.showModal = true;
}

const localCategories = ref<{id: number|null, name: string}[]>([...categories])
onBeforeMount(() => {
    localCategories.value.unshift({ id: null, name: 'Pilih kategori' })
})

const filters = reactive<FilterType>({
    keyword: search?.keyword || "",
    category_id: search?.category_id || null,
})


const debouceKeyword = useDebounce(computed(() => ({...filters})), 400);
watch(
    debouceKeyword,
    () => router.get('/products', {filters: filters}, {preserveState:true, replace:true}),
    {deep:true}
)

const strLimit = (str:string, limit = 40, end = '...') => str && str.length > limit ? str.slice(0, limit) + end : str || '';
</script>

<template>

    <Head title="Barang" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-2 pb-8">
            <div class="grid grid-cols-1 grid-rows-2 md:grid-cols-3 md:grid-rows-1 mx-6 mt-2 mb-6">
                <div>
                    <Alert class="bg-green-50 max-w-250 border-green-200 text-green-700" v-if="showMessage">
                        <Check class="h-4 w-4" />
                        <!-- <AlertTitle>Success</AlertTitle> -->
                        <AlertDescription>
                            {{ message }}
                        </AlertDescription>
                    </Alert>
                </div>
                <div class="flex justify-end col-span-2">
                    <select v-model="filters.category_id" class="select select-accent border-2 border-green-200 mr-2">
                        <template v-for="category in localCategories" :key="category.id">
                            <option :value="category.id">{{ category.name }}</option>
                        </template>
                    </select>
                    <label class="input input-md input-accent border-2 border-green-200 mr-4">
                        <SearchIcon class="text-green-300 text-sm"></SearchIcon>
                        <input v-model="filters.keyword" type="search" required placeholder="Search" />
                    </label>
                    <Link :href="route('products.create')">
                    <Button
                        class="bg-teal-400 hover:shadow-lg hover:shadow-teal-100 hover:bg-teal-500 cursor-pointer">Barang
                        Baru <Plus></Plus></Button>
                    </Link>
                </div>
            </div>
            <template v-if="products.data.length > 0">
            <div
                class="grid gap-6 mt-4 px-6 py-6 border-b border-gray-200 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div v-for="product in products.data" :key="product.id" v-bind="$attrs"
                    class="box-content md:box-border rounded-lg bg-white shadow-lg shadow-blue-500/10">
                    <div class="relative">
                        <div class="badge badge-info absolute right-3 top-3 px-2 text-white">{{ product.category?.name
                        }}</div>
                        <div
                            class="absolute w-full h-full z-10 bg-black opacity-0 hover:opacity-80 transition flex items-center justify-center gap-8 rounded-lg">
                            <Link :href="route('products.show', { id: product.id })"
                                class="relative inline-block group">
                            <button class="text-blue-900 cursor-pointer">
                                <ReceiptText></ReceiptText>
                            </button>
                            <div
                                class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                Lihat
                            </div>
                            </Link>
                            <Link :href="`/products/${product.id}/edit`" class="relative inline-block group">
                            <button class="text-green-900 cursor-pointer">
                                <Pencil></Pencil>
                            </button>
                            <div
                                class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                Ubah
                            </div>
                            </Link>
                            <a href="#" @click.prevent="handleModal" :data-url="`/products/${product.id}`"
                                class="relative inline-block group">
                                <button class="text-red-900 cursor-pointer ">
                                    <Trash></Trash>
                                </button>
                                <div
                                    class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                    Hapus
                                </div>
                            </a>
                        </div>
                        <img :src="product.image_path" class="h-56 w-full object-cover rounded-lg" />
                    </div>
                    <div class="grid grid-cols-1 justify-items-center p-4">
                        <h4 class="text-lg font-semibold text-gray-700">{{ product.name }}</h4>
                        <p class="mt-1 text-center text-sm text-gray-500">{{ strLimit(product.description) }}</p>
                        <p class="mt-1 text-center text-red-500">{{ formatCurrency(product.price_min) }}  <span v-if="Number(product.price_min) < Number(product.price_max)"> ~ {{ formatCurrency(product.price_max) }} </span></p>
                    </div>
                </div>
            </div>
            <CustomPaginate :pagination="products.meta" />
            </template>

            <ModalConfirm :modalData="modalData" @close-modal="modalData.showModal = false">
                <div class="grid grid-flow-col grid-rows-1 items-center justify-center gap-2 mb-4">
                    <OctagonAlert></OctagonAlert>
                    <h2 class="font-bold text-xl">Konfirmasi</h2>
                </div>
                <p>
                    Yakin menghapus barang ini?
                </p>
            </ModalConfirm>

        </div>
    </AppLayout>
</template>

<style lang="scss" scoped></style>