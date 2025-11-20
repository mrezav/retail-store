<script setup lang="ts">
import { Links, Meta } from '@/types/pagination';
import { VariantResource } from '@/types/variant';
import { reactive, ref, watch } from 'vue';
import { formatCurrency } from '../../utils/helper';
import { SearchIcon } from 'lucide-vue-next';
import { useDebounce } from '@vueuse/core';

const props = defineProps<{
    showModal: boolean,
    variantSelectedIDs: number[],
}>()

const localVariantSelected = reactive<VariantResource[]>([]);
let temporarySelected = reactive<VariantResource[]>([]);


const keyword = ref<string>('');

const modalRef = ref<HTMLDialogElement | null>(null)
interface Response {
    data: VariantResource[];
    links: Links;
    meta: Meta;
}
const variantsData = reactive<Response>({
    data: [],
    links: {
        first: '',
        last: '',
        prev: null,
        next: null,
    },
    meta: {
        current_page: 0,
        from: 0,
        last_page: 0,
        total: 0,
        per_page: 0,
        links: [],
    },
});

watch(
    () => props.showModal,
    async (newVal) => {
        if (newVal) {

            // Fetch variants data when modal is shown
            const url = '/get-variants';
            const result = await fetchData(url);
            variantsData.data = result.data;
            variantsData.links = result.links;
            variantsData.meta = result.meta;
            modalRef.value?.show();
            checkedData();
        }
    },
    { immediate: true }
)

function checkedData() {
    variantsData.data.forEach(variant => {
        if (props.variantSelectedIDs.includes(variant.id as number)) {
            const exists = localVariantSelected.some(v => v.id === variant.id);
            if (!exists){
                localVariantSelected.push(variant);
            }else{
                return;
            }
        }
    });
}

// const debounde = useDebounce(computed(() => ({...filters.keyword}), 500);
const debounce = useDebounce(keyword, 400)
watch(
    debounce,
    async (newVal) => {
        console.log('Filters changed >>> ', newVal);
        const queryParams = new URLSearchParams({
            keyword: newVal,
        });
        const url = `/get-variants?${queryParams.toString()}`;
        const result = await fetchData(url);
        variantsData.data = result.data;
        variantsData.links = result.links;
        variantsData.meta = result.meta;
        checkedData();
    },
    { deep: true }
)

async function fetchData(url: string): Promise<Response> {
    const res = await fetch(url).catch((error) => {
        console.error('Error fetching variants:', error);
    });
    const data = await res!.json();
    return new Promise<Response>(resolve => {
        resolve(data);
    });
    // return data;
}

const emit = defineEmits<{
    (e: 'close-modal'): void,
    (e: 'submit-modal', value: VariantResource[]): void,
}>()

function submitModal() {
    localVariantSelected.push(...temporarySelected);
    temporarySelected = [];
    modalRef.value?.close()
    emit('submit-modal', localVariantSelected);
}

function closeModal() {
    temporarySelected = [];
    console.log(temporarySelected)
    modalRef.value?.close()
    emit('close-modal')
}

const handlePageChange = async (url: string | null) => {
    if (!url) return;
    console.log('Page URL >>> ', url);
    const result = await fetchData(url);
    variantsData.data = result.data;
    variantsData.links = result.links;
    variantsData.meta = result.meta;
    checkedData();
}
function numberList(i: number) {
    if (variantsData.data.length > 0) {
        const currentPage = variantsData.meta.current_page;
        const perPage = variantsData.meta.per_page;

        return i + ((currentPage - 1) * perPage)
    }
}

function handleCheck(id: number | null, e: Event) {
    // Ensure the event target is an HTMLInputElement before accessing 'checked'
    if (!(e.target instanceof HTMLInputElement)) return;
    const checked = e.target.checked;
    console.log(checked)
    if (checked) {
        addVariant(id);
    } else {
        removeVariant(id);
    }
}

function addVariant(id: number | null) {
    if (id === null) return;
    const existsInLocal = localVariantSelected.some(v => v.id === id);
    if (existsInLocal) return;
    const existsInTemp = temporarySelected.some(v => v.id === id);
    if (existsInTemp) return;
    const variant = variantsData.data.find(v => v.id === id);
    if (variant) {
        temporarySelected.push(variant);
    }
}

function removeVariant(id: number | null) {
    if (id === null) return;
    const indexInTemp = temporarySelected.findIndex(v => v.id === id);
    if (indexInTemp !== -1) {
        temporarySelected.splice(indexInTemp, 1);
        return;
    }
    const indexInLocal = localVariantSelected.findIndex(v => v.id === id);
    if (indexInLocal !== -1) {
        localVariantSelected.splice(indexInLocal, 1);
    }
}

function handleIsChecked(id: number | null) {
    // console.log('Checking id >>> ', id);
    if (id === null) return false;
    const temp = temporarySelected.some(v => v.id === id);
    const local = localVariantSelected.some(v => v.id === id);
    const check = temp || local;
    return check;
}
</script>

<template>
    <teleport to="body">
        <dialog ref="modalRef" class="modal">
            <div class="modal-box w-11/12 max-w-5xl">
                <label class="input input-md input-accent border-2 border-green-200 mr-4 mb-4 flex items-center gap-2">
                    <SearchIcon class="text-green-300 text-sm"></SearchIcon>
                    <input v-model="keyword" type="search" placeholder="Search" />
                </label>
                <div v-if="variantsData.data.length <= 0" class="min-h-60 min-w-full">
                    <div class="grid grid-rows-2 justify-center mt-12">
                        <div class="grid place-items-center">
                            <img src="/notfound.png" class="w-40 opacity-60">
                        </div>
                        <div class="grid item-center mt-8">
                            <h3 class="opacity-60 text-2xl font-bold">Varian tidak ditemukan!</h3>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <table class="table table-zebra bg-gray-50">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="w-12 text-center">#</th>
                                <th class="w-120">Barang</th>
                                <th class="w-120">Merk/Jenis</th>
                                <th>Warna</th>
                                <th>Ukuran</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(variant, i) in variantsData.data" :key="variant.id">
                                <tr>
                                    <td>{{ numberList(i + 1) }}</td>
                                    <td>{{ variant.product.name }}</td>
                                    <td>{{ variant.merk }}</td>
                                    <td>{{ variant.color }}</td>
                                    <td>{{ variant.dimension }}</td>
                                    <td>{{ variant.stock }}</td>
                                    <td class="text-red-500 font-bold text-md">{{ formatCurrency(variant.price) }}</td>
                                    <td>
                                        <input @change="handleCheck(variant.id, $event)"
                                            :checked="handleIsChecked(variant.id)" type="checkbox"
                                            class="checkbox checkbox-primary bg-gray-300" />
                                        </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div>
                        <nav class="flex items-center justify-center gap-2 mt-6">
                            <template v-for="link in variantsData.meta.links" :key="link.label">
                                <!-- <Link preserve-scroll> -->
                                <button type="button" @click="handlePageChange(link.url)"
                                    class="flex item-center justify-center px-4 py-2 rounded-lg text-gray-500 text-sm"
                                    :class="{ 'bg-blue-500 text-white': link.active, 'hover:bg-gray-200': !link.active, '!text-gray-300 cursor-not-allowed': !link.url, 'cursor-pointer': link.url }"
                                    :disabled="!link.url">
                                    <span v-html="link.label"></span>
                                </button>
                                <!-- </Link> -->
                            </template>
                        </nav>
                    </div>
                    <div class="modal-action">
                        <!-- if there is a button in form, it will close the modal -->
                        <button @click="submitModal" class="btn btn-outline btn-accent">Tambah</button>
                        <button @click="closeModal" class="btn btn-outline">Batal</button>
                    </div>
                </div>
            </div>
        </dialog>
    </teleport>
</template>

<style lang="css" scoped></style>