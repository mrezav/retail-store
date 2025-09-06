<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    modalData: {
        showModal: boolean,
        targetUrl: string,
    }
}>()

const emit = defineEmits<{
    (e: 'update', value: boolean): void
}>()

</script>

<template>
    <Teleport to="body">
        <div class="fixed overflow-x-auto overflow-y-auto inset-0 flex justify-center items-center z-20 "
            v-if="modalData.showModal">
            <div class="relative mx-auto width-auto shadow-xl-20 max-w-2xl">
                <div class="bg-white w-full p-10  rounded-lg shadow-lg text-center">
                    <slot></slot>
                    <Link :href="modalData.targetUrl" @click="emit('update', false)">
                    <button class="bg-red-600 text-white px-4 py-2 rounded mt-4 hover:bg-red-700 cursor-pointer mr-2">Yes</button>
                    </Link>
                    <button @click="emit('update', false)" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 cursor-pointer">No</button>
                    <div @click="emit('update', false)" class="absolute top-0 right-0 m-4 cursor-pointer text-gray text-2xl font-bold">x</div>
                </div>
            </div>
        </div>
        <div v-if="modalData.showModal" class="fixed inset-0 z-10 opacity-60 bg-black "></div>
    </Teleport>
</template>

<style lang="scss" scoped></style>