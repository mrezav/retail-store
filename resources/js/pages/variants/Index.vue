<script setup lang="ts">
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Link, Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js'
import { Plus, Check, Trash, SquarePen, OctagonAlert } from 'lucide-vue-next';
import Button from '@/components/ui/button/Button.vue';
import { useDebounce } from '@vueuse/core';
import { SearchIcon } from 'lucide-vue-next';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Links, Meta } from '@/types/pagination';
import { VariantForm, VariantResource } from '@/types/variant';
import { formatCurrency } from '@/utils/helper';
import CustomPaginate from '@/components/custom/CustomPaginate.vue';
import ModalConfirm from '@/components/custom/ModalConfirm.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Varian',
        href: '/variants',
    },
];

// const baseUrl = window.location.origin;

interface FilterType {
    keyword: string,
    category_id: number | null,
    product_id: number | null,
    sort_by: string | null,
    sort_type: string | null,
}

interface Response {
    data: VariantResource[],
    links: Links,
    meta: Meta,
}

const sortKey = ref<keyof VariantResource | null>(null);
const sortAsc = ref(true);

const pageData = usePage()
const showMessage = ref<boolean>(false);
const message = ref<string>('');
const flash = (pageData.props as { flash?: { message?: string } }).flash;
if (flash && flash.message) {
    showMessage.value = true;
    message.value = flash.message;
    setTimeout(() => {
        showMessage.value = false;
    }, 8000);
}

const { search, products, variants } = defineProps<{
    search: FilterType | null,
    products: { id: number | null, name: string }[],
    variants: Response
}>();

const localProducts = ref<{ id: number | null, name: string }[]>([{ id: null, name: 'Pilih Barang' }, ...products])

const filters = reactive<FilterType>({
    keyword: search?.keyword || "",
    category_id: search?.category_id || null,
    product_id: search?.product_id || null,
    sort_by: '',
    sort_type: '',
})

const debounce = useDebounce(computed(() => ({ ...filters })), 400)
watch(debounce,
    () => {
        router.get(route('variants.index'), { filters: filters }, { preserveState: true, preserveScroll: true, replace: true })
    },
    { deep: true }
)

function numberList(i: number) {
    if (variants.data.length > 0) {
        const currentPage = variants.meta.current_page;
        const perPage = variants.meta.per_page;

        return i + ((currentPage - 1) * perPage)
    }
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

function sortTable(key: keyof VariantForm) {
    sortAsc.value = !sortAsc.value
    sortKey.value = key
    filters.sort_by = key
    if (sortAsc.value) {
        filters.sort_type = 'asc'
    } else {
        filters.sort_type = 'desc'
    }
}

const sourceImage = ref<string|null>()
const modalImageRef = ref<HTMLDialogElement>()
function imageZoom(source: string|null){
    modalImageRef.value?.show()
    sourceImage.value = source
}
</script>

<template>

    <Head title="Varian" />

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
                    <select v-model="filters.product_id" class="select select-accent border-2 border-green-200 mr-2">
                        <template v-for="product in localProducts" :key="product.id">
                            <option :value="product.id">{{ product.name }}</option>
                        </template>
                    </select>
                    <label class="input input-md input-accent border-2 border-green-200 mr-4">
                        <SearchIcon class="text-green-300 text-sm"></SearchIcon>
                        <input v-model="filters.keyword" type="search" required placeholder="Search" />
                    </label>
                    <Link :href="route('variants.create')">
                    <Button type="button btn-accent"
                        class="bg-teal-400 hover:shadow-lg hover:shadow-teal-100 hover:bg-teal-500 cursor-pointer">
                        Varian Baru <Plus></Plus>
                    </Button>
                    </Link>
                </div>
            </div>
            <div v-if="variants.data.length > 0" class="mx-6">
                <table class="table table-zebra bg-gray-50">
                    <!-- head -->
                    <thead>
                        <tr class="bg-teal-200">
                            <th class="w-12 text-center">#</th>
                            <th class="text-center">Barang</th>
                            <th @click="sortTable('merk')" class="cursor-pointer">Merk/Jenis
                                <span v-if="sortKey === 'merk'" class="ml-2">
                                    {{ sortAsc ? "▲" : "▼" }}
                                </span>
                            </th>
                            <th>Warna</th>
                            <th>Ukuran</th>
                            <th @click="sortTable('stock')" class="cursor-pointer px-2">Stok 
                                <span v-if="sortKey === 'stock'" class="ml-2">
                                    {{ sortAsc ? "▲" : "▼" }}
                                </span>
                            </th>
                            <th @click="sortTable('price')" class="cursor-pointer px-2">Harga <span
                                    v-if="sortKey === 'price'" class="ml-2">
                                    {{ sortAsc ? "▲" : "▼" }}
                                </span>
                            </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(variant, i) in variants.data" :key="i">
                            <tr class="capitalize">
                                <td class="text-center">{{ numberList(i + 1) }}</td>
                                <td class="grid text-center items-center justify-center">
                                    <template v-if="variant.image_path != '' && variant.image_path != undefined">
                                        <img :src="variant.image_path" @click="imageZoom(variant.image_path)" class="w-26 h-auto rounded-lg object-fit cursor-pointer"/>
                                    </template>
                                    <template v-else-if="variant.product?.image_path">
                                        <img :src="variant.product?.image_path" @click="imageZoom(variant.product?.image_path)" class="w-26 h-auto rounded-lg object-fit cursor-pointer"/>
                                    </template>
                                    <template v-else>
                                        <!-- <img src="/images.jpg" @click="imageZoom('/images.jpg')" class="w-26 h-auto rounded-lg object-fit cursor-pointer"/> -->
                                        <div class="w-26 h-20 bg-gray-200 flex items-center justify-center rounded-lg">
                                            <span class="text-gray-500">No Image</span>
                                        </div>
                                    </template>
                                    <span class="font-bold text-gray-400 mt-2">{{ variant.product?.name }}</span>
                                </td>
                                <td class="font-bold text-gray-500">{{ variant.merk }}</td>
                                <td>{{ variant.color }}</td>
                                <td>{{ variant.dimension }}</td>
                                <td>{{ variant.stock }}</td>
                                <td class="text-red-500 font-bold text-md">{{ formatCurrency(variant.price) }} </td>
                                <td class="text-center">
                                    <Link :href="route('variants.edit', { id: variant.id })"
                                        class="relative inline-block group">
                                        <button class="text-yellow-400 cursor-pointer">
                                            <SquarePen></SquarePen>
                                        </button>
                                        <div
                                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                            Ubah
                                        </div>
                                    </Link>
                                    <a href="#" @click.prevent="handleModal" :data-url="`/variants/${variant.id}`"
                                        class="relative inline-block group">
                                        <button class="text-red-400 cursor-pointer ">
                                            <Trash></Trash>
                                        </button>
                                        <div
                                            class="absolute hidden group-hover:block bg-gray-700 text-white text-xs rounded py-1 px-2 bottom-full left-1/2 transform -translate-x-1/2 mb-2">
                                            Hapus
                                        </div>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>

                </table>
                <CustomPaginate :pagination="variants.meta"></CustomPaginate>
            </div>
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
        </div>

        <dialog id="zoom_image" ref="modalImageRef" class="modal">
            <div class="modal-box w-auto max-w-none max-w-[60vw] max-h-[80vh] overflow-auto p-4">
                <img :src="sourceImage??undefined" class="object-contain rounded-lg" />
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <ModalConfirm :modalData="modalData" @close-modal="modalData.showModal = false">
            <div class="grid grid-flow-col grid-rows-1 gap-2 mb-4">
                <OctagonAlert></OctagonAlert>
                <h2 class="font-bold text-xl">Konfirmasi</h2>
            </div>
            <p>
                Yakin menghapus varian ini?
            </p>
        </ModalConfirm>


    </AppLayout>

</template>

<style lang="scss" scoped></style>