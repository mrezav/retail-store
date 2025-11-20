<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { TransactionForm } from '../../types/transaction';
import { computed, ref, watch } from 'vue';
import TransactionDetailForm from '@/types/transaction_detail';
import Button from '@/components/ui/button/Button.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { OctagonAlert, Plus, } from 'lucide-vue-next';
import ModalCheck from '../variants/ModalCheck.vue';
import { VariantResource } from '@/types/variant';
import { formatCurrency, terbilang } from '@/utils/helper';
import { route } from 'ziggy-js';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Transactions',
        href: '/transactions/create',
    }
]
const getNow = function () {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

let variantSelectedIDs = ref<number[]>([]);
// const pageData = usePage()
// const showFlash = ref<boolean>(false);
// const flashMessage = ref<string>('');
// watch(
//     () => pageData.props,
//     (newVal) => {
//         console.log('Page props changed >>> ', newVal);
//         const flash = (newVal as {flash? : {message?: string}}).flash;
//         console.log('flash >>>>', flash)
//         if (flash && flash.message) {
//             showFlash.value = true;
//             flashMessage.value = flash.message;
//             // flashMessage.value = 'Terdapat kesalahan input data. Silahkan periksa kembali atau hubungi super admin.';
//             setTimeout(() => {
//                 showFlash.value = false;
//             }, 10000);
//         }
//     },
//     { deep: true }
// )

const detailForm = ref<TransactionDetailForm[]>([]);
const form = useForm<TransactionForm>({
    id: null,
    user_id: 0,
    buyer_id: 0,
    buyer_name: '',
    total_price: 0,
    additional_cost: 0,
    discount: 0,
    grand_total: 0,
    is_installment: false,
    due_date: null,
    transaction_date: getNow(),
    transaction_detail: detailForm.value,
    payment_nominal: 0,
    _method: 'post',
});
const money_back = computed(() => {
    return form.payment_nominal - form.total_price;
});

const checkNow = ref<boolean>(true);
const checkref = ref<HTMLInputElement | null>(null);

function checkDate() {
    if (checkref.value?.checked) {
        form.transaction_date = getNow();
    } else {
        form.transaction_date = '';
    }
}

const showModal = ref<boolean>(false);

function addProduct() {
    showModal.value = true;
}

watch(detailForm, (newVal) => {
    form.total_price = newVal.reduce((acc, item) => acc + item.sub_total, 0);
    form.grand_total = (form.total_price + form.additional_cost) - form.discount;
    newVal.forEach(item => {
        item.sub_total = item.quantity * item.price;
    });
    form.transaction_detail = newVal;
}, { deep: true });

const modalConfirmRef = ref<HTMLDialogElement | null>(null);
function submitForm() {
    console.log('Submit Form >>> ', money_back.value);
    if (money_back.value < 0 && !form.is_installment) {
        modalConfirmRef.value?.click();
    } else {
        console.log('Submitting form...');
        form.post(route('transactions.store'), {
            onSuccess: () => {
                form.reset();
            },
            onError: (err) => {
                console.log('error >>>>', err)
            }
        });
    }
}

function handleSubmitModal(selectedVariants: VariantResource[]) {
    console.log('Selected Variants >>> ', selectedVariants);
    selectedVariants.forEach(variant => {
        // Check if variant already exists in detailForm
        const exists = detailForm.value.some(detail => detail.variant_id === variant.id);
        if (!exists) {
            detailForm.value.push({
                id: null,
                transaction_id: null,
                product_id: variant.product_id ?? 0,
                variant_id: variant.id ?? 0,
                product_name: variant.product?.name ?? '',
                variant_merk: variant.merk,
                variant_unit: variant.unit,
                variant_color: variant.color,
                variant_dimension: variant.dimension ?? '',
                quantity: 1,
                price: variant.price,
                sub_total: variant.price,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),
                _method: 'post'
            });
        }
    });
    showModal.value = false;
    variantSelectedIDs = ref<number[]>(selectedVariants.map(v => v.id as number));

}

function handleDeleteSelectedVariant(i: number, variant_id?: number) {
    console.log('Delete variant from detailForm');
    detailForm.value.splice(i, 1)
    if (variant_id) {
        const index = variantSelectedIDs.value.indexOf(variant_id);
        if (index > -1) {
            variantSelectedIDs.value.splice(index, 1);
        }
    }
}

function handleCloseModal() {
    showModal.value = false;
}


</script>

<template>

    <Head title="Transactions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col items-center">
            <div class="w-11/12 py-6">
                <h1 class="text-2xl font-semibold mb-4">Buat Transaksi Baru</h1>
                <form @submit.prevent="submitForm" class="space-y-8 divide-y divide-gray-900/10">
                    <div class="grid gap-4 border-b border-gray-900/10 pb-12">
                        <div class="grid md:grid-cols-4 grid-cols-2 gap-3">
                            <div>
                                <label for="buyer_name" class="block text-sm/6 font-medium text-gray-600">Nama Pembeli
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input v-model="form.buyer_name" type="text" name="buyer_name" id="buyer_name"
                                        placeholder="Nama Pembeli"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.buyer_name }"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.buyer_name">{{
                                    form.errors.buyer_name }}</p>
                            </div>
                            <!-- <div class="mt-8">
                                <Button
                                    class="rounded bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300 cursor-pointer">Cari
                                    Pelanggan</Button>
                            </div> -->
                            <div>
                                <label for="transaction_date" class="block text-sm/6 font-medium text-gray-600">Tanggal
                                    <span class="text-red-500">*</span></label>
                                <div class="mt-2">
                                    <input v-model="form.transaction_date" type="date" name="transaction_date"
                                        id="transaction_date" placeholder="Email"
                                        :class="{ 'outline-red-300 focus:outline-red-400': form.errors.transaction_date }"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                                </div>
                                <p class="mt-2 text-sm text-red-600" v-if="form.errors.transaction_date">{{
                                    form.errors.transaction_date }}</p>
                            </div>
                            <div class="mt-9">
                                <input ref="checkref" @click="checkDate" v-model="checkNow" type="checkbox"
                                    class="checkbox checkbox-primary bg-gray-300" /> Sekarang
                            </div>
                        </div>
                    </div>
                    <div>
                        <Button @click.prevent="addProduct"
                            class="bg-teal-400 hover:shadow-lg hover:shadow-teal-100 hover:bg-teal-500 cursor-pointer">Pilih
                            Barang <Plus></Plus></Button>
                    </div>
                    <div v-if="detailForm.length > 0">
                        <div class="mt-4 flex items-center justify-between gap-2">
                            <div>
                                <input v-model="form.is_installment" type="checkbox" name="is_installment"
                                class="checkbox checkbox-primary bg-gray-300 mr-2"> Pembayaran Cicilan
                            </div>
                            <div>
                                <!-- Meminjam key id untuk pesan error saat penyimpanan transaksi gagal -->
                                <Alert class="bg-red-50 max-w-200 border-red-200" v-if="form.errors.is_installment">
                                    <!-- <Check class="h-4 w-4" /> -->
                                    <!-- <AlertTitle>Success</AlertTitle> -->
                                    <AlertDescription>
                                        <span class="text-red-600">{{ form.errors.is_installment }}</span>
                                    </AlertDescription>
                                </Alert>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center">
                            <table class="table-auto min-w-full border-collapse border border-gray-200 mt-4">
                                <thead class="bg-teal-300">
                                    <tr>
                                        <th class="px-4 py-2 border">#</th>
                                        <th class="px-4 py-2 border">Produk</th>
                                        <th class="px-4 py-2 border">Varian</th>
                                        <th class="px-4 py-2 border">Dimensi</th>
                                        <th class="px-4 py-2 border">Warna</th>
                                        <th class="px-4 py-2 border">Kuantitas</th>
                                        <th class="px-4 py-2 border">Harga</th>
                                        <th class="px-4 py-2 border">Sub Total</th>
                                        <th class="px-4 py-2 border">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(detail, index) in detailForm" :key="index">
                                        <td class="px-4 py-2 border">{{ index + 1 }}</td>
                                        <td class="px-4 py-2 border">{{ detail.product_name }}</td>
                                        <td class="px-4 py-2 border">{{ detail.variant_merk }}</td>
                                        <td class="px-4 py-2 border">{{ detail.variant_dimension }}</td>
                                        <td class="px-4 py-2 border">{{ detail.variant_color }}</td>
                                        <td class="px-4 py-2 border flex items-center justify-center">
                                            <input type="number" name="quantity" step="0.01" min="1" v-model="detail.quantity"
                                                class="input input-sm text-lg w-15 mr-1"/> 
                                                <div>
                                                    ({{ detail.variant_unit }})
                                                </div>
                                        </td>
                                        <td class="px-4 py-2 border text-red-500 font-bold text-md">{{
                                            formatCurrency(detail.price) }}</td>
                                        <td class="px-4 py-2 border text-red-500 font-bold text-md">{{
                                            formatCurrency(detail.sub_total) }}</td>
                                        <td class="px-4 py-2 border">
                                            <button @click="handleDeleteSelectedVariant(index, detail.variant_id)"
                                                class="btn btn-sm btn-outline btn-error">Hapus</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-sm text-green-600 italic">{{ terbilang(form.total_price) }}</td>
                                        <td colspan="2" class="px-4 py-2 border font-bold text-right">Total Harga</td>
                                        <td class="px-4 py-2 border text-red-500 font-bold text-md">{{
                                            formatCurrency(form.total_price) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-sm text-green-600 italic">{{ terbilang(form.payment_nominal) }}</td>
                                        <td colspan="2" class="px-4 py-2 border font-bold text-right">Jumlah Bayar</td>
                                        <td class="px-4 py-2 border font-bold text-md">
                                            <input type="number" placeholder="0" v-model="form.payment_nominal"
                                                class="input w-32" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-sm text-green-600 italic">{{ terbilang(money_back) }}</td>
                                        <td colspan="2" class="px-4 py-2 border font-bold text-right">Kembalian</td>
                                        <td class="px-4 py-2 border font-bold text-md text-green-600">
                                            {{ money_back < 0 ? formatCurrency(0) : formatCurrency(money_back) }} </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                        </div>
                    </div>
                </form>
                <ModalCheck v-if="showModal" :variantSelectedIDs="variantSelectedIDs" :showModal="showModal"
                    @submit-modal="handleSubmitModal" @close-modal="handleCloseModal" />
            </div>

            <!-- The button to open modal -->
            <!-- <label for="modal_confirm" class="btn">open modal</label> -->

            <!-- Put this part before </body> tag -->
            <input type="checkbox" id="modal_confirm" ref="modalConfirmRef" class="modal-toggle" />
            <div class="modal" role="dialog">
                <div class="modal-box">
                    <div class="grid grid-flow-col grid-rows-1 items-center justify-center gap-2 mb-4">
                        <OctagonAlert></OctagonAlert>
                        <h3 class="text-lg font-bold">INFO</h3>
                    </div>
                    <p class="py-4 text-center">Jumlah pembayaran kurang dari total harga, silahkan centang pembayaran
                        cicilan untuk melanjutkan transaksi!</p>
                    <div class="modal-action grid justify-center">
                        <label for="modal_confirm" class="btn btn-info text-white">Mengerti</label>
                    </div>
                </div>
                <label class="modal-backdrop" for="modal_confirm">Close</label>
            </div>
        </div>
    </AppLayout>
</template>

<style lang="scss" scoped></style>