<template>
    <app-layout title="Products">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"
                v-html="editFlag ? product.name+ '(Edit Product)' : 'Create Product'">
            </h2>
        </template>
        <template #actions>
            <a :href="route('products.index')">
                <Button label="Back" icon="pi pi-arrow-left" iconPos="left" />
            </a>
        </template>

        <form @submit.prevent="submitForm">
            <div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8">
                <div class="flex-wrap flex">
                    <div class="w-full pb-6 md:pb-0 md:pr-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="p-fluid p-formgrid p-grid">
                                    <div class="p-field p-col">
                                        <label for="name" class="font-semibold">Name<span class="ml-1 text-red-400">*</span></label>
                                        <InputText id="name" type="text" v-model="form.name" placeholder="Enter Product Name" :class="[errors.name ? 'p-invalid' : '']"/>
                                        <small id="name-help" v-if="errors.name" class="p-invalid">{{ errors.name }}</small>
                                    </div>
                                    <div class="p-field p-col">
                                        <label for="upc" class="font-semibold">UPC<span class="ml-1 text-red-400">*</span></label>
                                        <InputText id="upc" type="text" v-model="form.upc" placeholder="Enter Universal Product Code" :class="[errors.upc ? 'p-invalid' : '']"/>
                                        <small id="upc-help" v-if="errors.upc" class="p-invalid">{{ errors.upc }}</small>
                                    </div>
                                </div>
                                <div class="p-fluid p-formgrid p-grid">
                                    <div class="p-field p-col">
                                        <label for="price" class="font-semibold">Price<span class="ml-1 text-red-400">*</span></label>
                                        <InputText id="price" type="text" v-model="form.price" placeholder="Enter Price" :class="[errors.price ? 'p-invalid' : '']"/>
                                        <small id="price-help" v-if="errors.upc" class="p-invalid">{{ errors.price }}</small>
                                    </div>
                                    <div class="p-field p-col mt-5">
                                        <label for="status" class="font-semibold">Status<span class="ml-1 text-red-400">*</span></label>
                                        <InputSwitch v-model="form.status" class="pt-5 ml-5" />
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <img :src="form.image" class="img-responsive" height="70" width="90" >
                                    </div>
                                    <div class="col-md-6">
                                        <input type="file" ref="file" v-on:change="onImageChange" class="form-control">
                                    </div>

                                </div>

                                <div class="w-full flex mt-4">
                                    <Button type="submit" class="p-button-raised" icon="pi pi-check" :label="editFlag ? 'Update Product' : 'Add Product'" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import InputText from 'primevue/inputtext';
    import InputSwitch from 'primevue/inputswitch';
    import Button from 'primevue/button';
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'


    export default defineComponent({
        name: 'ProductEntry',
        components: {
            AppLayout,
            InputText,
            InputSwitch,
            Button,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetActionMessage,
            JetSecondaryButton,

        },
        props: {
            editFlag: {
                type: Boolean,
                default: false,
            },
            product: {
                type: Object,
                default: null,
            },
            errors: Object,
            name: '',
        },
        data() {
            return {
                form: {
                    name: this.product !== null ? this.product.name : '',
                    upc: this.product !== null ? this.product.upc : '',
                    price: this.product !== null ? this.product.price : '',
                    image: this.product !== null ? this.product.image : '',
                    status: true,
                },
                formValidated: false,
                loading: false,
                imagePreview: null,
            }
        },
        methods: {


            onImageChange(e) {
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                let vm = this;
                reader.onload = (e) => {
                    vm.form.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },


            submitForm() {
                this.editFlag ? this.update() : this.create();
            },
            create() {
                this.formValidated = true;
                this.$inertia.post(route('products.store'), this.form, {
                    onSuccess: () => {
                        if (Object.keys(this.errors).length === 0) {
                            this.$emit('close', true);
                        }
                    },
                });
            },
            update() {
                this.formValidated = true;
                this.$inertia.patch(route('products.update', { id: this.product.id }), this.form, {
                    onSuccess: () => {
                        if (Object.keys(this.errors).length === 0) {
                            this.$emit('close', true);
                        }
                    },
                });
            },
        },
    })
</script>
