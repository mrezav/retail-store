<script setup lang="ts">
import { watch, ref } from 'vue';
import { VariantForm } from '@/types/variant';
import { useForm } from '@inertiajs/vue3';
import { validation, variantDefaultForm } from '@/utils/variant';

const { showModal, variantData, variantIndex } = defineProps<{
    showModal: boolean,
    variantData: VariantForm | null,
    variantIndex: number | null,
}>()

let form = useForm<VariantForm>(variantDefaultForm('post'))

function handleAction() {
    form = validation(form)
    if (form.hasErrors) {
        console.log('error >>>', form.hasErrors)
        return
    }

    const variant: VariantForm = {
        id: form.id,
        product_id: form.product_id,
        merk: form.merk,
        unit: form.unit,
        color: form.color,
        dimension: form.dimension,
        stock: form.stock,
        price: form.price,
        description: form.description,
        image: null,
        image_path: '',
        image_preview: '',
        _method: null,
    }

    if (variantData) {
        emit('edit-variant', variant, variantIndex)
    } else {
        emit('add-variant', variant)
    }
    form.reset()
    form.errors = {}
    modalRef.value?.close()
}

const modalRef = ref<HTMLDialogElement | null>(null)

watch(
    () => showModal,
    () => {
        if (showModal) {
            modalRef.value?.show()
            if (variantData) {
                form.id = variantData.id
                form.product_id = variantData.product_id
                form.merk = variantData.merk
                form.unit = variantData.unit
                form.color = variantData.color
                form.dimension = variantData.dimension
                form.stock = variantData.stock
                form.price = variantData.price
                form.description = variantData.description
            };
        }
    }
)


const emit = defineEmits<{
    (e: 'close'): void
    (e: 'add-variant', value: VariantForm): void,
    (e: 'edit-variant', value: VariantForm, index: number | null): void
}>()

function closeModal() {
    form.reset()
    form.errors = {}
    modalRef.value?.close()
    emit('close')
}
</script>

<template>
    <Teleport to="body">
        <dialog ref="modalRef" class="modal">
            <form @submit.prevent="handleAction">
                <div class="modal-box w-11/12 max-w-5xl">
                    <div class="mb-4 text-lg font-bold">
                        Varian Barang
                    </div>
                    {{ variantData }}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Merk/Jenis <span
                                    class="text-red-500">*</span></label>
                            <input type="text" v-model="form.merk" placeholder="Masukkan merk atau jenis"
                                class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            <p v-if="form.errors.merk" class="text-sm text-red-500 mt-1">{{
                                form.errors.merk }}</p>
                        </div>
                        <div>
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Warna </label>
                            <input type="text" v-model="form.color" placeholder="Masukkan warna"
                                class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                        </div>
                        <div>
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Dimensi/Ukuran</label>
                            <input type="text" v-model="form.dimension" placeholder="5cm x 5cm / S / M / L"
                                class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-4 mt-4">
                        <div class="col-span-2">
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Stok <span
                                    class="text-red-500">*</span></label>
                            <input type="number" placeholder="0" step="1" name="stock" v-model="form.stock"
                                class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            <p v-if="form.errors.stock" class="text-sm text-red-500 mt-1">{{
                                form.errors.stock }}</p>
                        </div>
                        <div>
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Satuan <span
                                    class="text-red-500">*</span></label>
                            <input type="text" v-model="form.unit" placeholder="Pcs/Cm/Kg/M&sup3;"
                                class="rounded-md w-full bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6">
                            <p v-if="form.errors.unit" class="text-sm text-red-500 mt-1">{{
                                form.errors.unit }}</p>
                        </div>
                        <div class="col-span-3">
                            <label for="merk" class="block text-sm/6 font-medium text-gray-600">Harga <span
                                    class="text-red-500">*</span></label>
                            <div
                                class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 has-[input:focus-within]:outline-2 has-[input:focus-within]:-outline-offset-2 has-[input:focus-within]:outline-indigo-600">
                                <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6">Rp</div>
                                <input id="price" type="number" step="1" name="price" placeholder="0.000"
                                    v-model="form.price"
                                    class="block min-w-0 grow py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6" />
                            </div>
                            <p v-if="form.errors.price" class="text-sm text-red-500 mt-1">{{
                                form.errors.price }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="description" class="block text-sm/6 font-medium text-gray-600">Deskripsi</label>
                        <textarea v-model="form.description" id="description" name="description" rows="3"
                            class="mt-2 block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-400 sm:text-sm/6"
                            placeholder="Deskripsi singkat tentang varian ini..."></textarea>

                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-outline btn-accent">Tambah</button>
                        <button class="btn btn-outline" @click.prevent="closeModal">Tutup</button>
                    </div>
                </div>
            </form>
        </dialog>
    </Teleport>
</template>

<style lang="scss" scoped></style>