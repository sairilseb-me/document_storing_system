<template>
    <v-dialog 
        max-width="800px"
        v-model="visible"
    >
        <v-card>
            <v-card-title class="d-flex justify-center items-center mt-3">
                View File
                <v-spacer></v-spacer>
                <v-icon @click="closeDialog">
                    mdi-close
                </v-icon>
            </v-card-title>
            <v-card-text>
                <v-text-field class="mb-3" label="Title" v-model="title" :readonly="!isEdit"></v-text-field>
                <office-autocomplete v-model="office_id" class="mb-3" :readonly="!isEdit"></office-autocomplete>
                <v-text-field class="mb-3" label="Date Received" v-model="date_received" :readonly="!isEdit"></v-text-field>
                <v-textarea class="mb-3" label="Remarks" v-model="remarks" :readonly="!isEdit"></v-textarea>
                <div v-if="isFilePDF && !isEdit">
                    <iframe :src="returnFilePath" width="100%" height="800px" frameborder="0"></iframe>
                </div>
                <v-btn v-else color="primary" @click="downloadFile" :loading="loading">Download</v-btn>
            </v-card-text>
            <v-card-actions>
                <v-btn color="secondary" @click="closeDialog">Close</v-btn>
                <v-spacer></v-spacer>
                <v-btn v-if="!isEdit" color="warning" @click="isEdit = !isEdit">Edit</v-btn>
                <v-btn v-else color="primary" @click="updateFile" :loading=loading>Update</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import { ref, watch, computed } from 'vue'
import axios from '@axios'
import OfficeAutocomplete from '@/components/autocompletes/office-autocomplete.vue'
import { useGlobalSnackbarStore } from '@/store/GlobalSnackbar'
export default {
    components: {
        OfficeAutocomplete,
    },
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
        file: {
            type: Object,
            default: null,
        },
        
    },
    setup(props, {emit}) {
        
        const visible = ref(false)
        const file = ref(props.file)
        const isEdit = ref(false)
        const title = ref(null)
        const office_id = ref(null)
        const date_received = ref(null)
        const remarks = ref(null)
        const globalSnackbar = useGlobalSnackbarStore()
        const loading = ref(false)
        
        watch(
            () => props.visible,
            (value) => {
                visible.value = value
            }
        )


        const isFilePDF = computed(() => {
            if (props.file && props.file.path){
                return props.file.path.split('.').pop() === 'pdf' ? true : false
            }
        })

        const returnFilePath = computed(() => {
            if (props.file && props.file.path){
                console.log(import.meta.env.VITE_APP_URL + '/storage/' + props.file.path)
                return import.meta.env.VITE_APP_URL + '/storage/' + props.file.path
            }
        })

        watch(
            () => props.file,
            (value) => {
                if (value && value.id){
                    file.value = value
                    title.value = value.title
                    office_id.value = value.office_id
                    date_received.value = value.date_received
                    remarks.value = value.remarks
                }
               
            }
        )

        const updateFile = () => {
            loading.value = true
            axios.put(`/file/${props.file.id}`, {
                title: title.value,
                office_id: office_id.value,
                date_received: date_received.value,
                remarks: remarks.value
            })
            .then(response => {
                if (response.status == 200){
                    globalSnackbar.setValues({
                        show: true,
                        message: 'File updated successfully',
                        color: 'success'
                    })
                    closeDialog()
                }
            })
            .catch(error => {
                console.log(error)
            }).finally(() => {
                loading.value = false
            })  
        }
    
        const downloadFile = async() => {
            try {
                const response = await axios.get(`/file-download/${file.value.id}`, {
                    responseType: 'blob'
                })
                const url = window.URL.createObjectURL(new Blob([response.data]))
                const link =document.createElement('a')
                link.href = url
                let filename_array = file.value.path.split('/')
                let filename = filename_array[filename_array.length - 1]
                link.setAttribute('download', filename)
                document.body.appendChild(link)
                link.click()
                document.body.removeChild(link)
                window.URL.revokeObjectURL(url)
            }catch (error){
                console.log(error)
            }

            loading.value = false
        }

        const closeDialog = () => {
            emit('close')
            isEdit.value = false
        }
        
        return {
            //variables
            visible,
            file,
            isEdit,
            title,
            office_id,
            remarks,
            date_received,
            loading,

            //compputed
            isFilePDF,
            returnFilePath,

            //methods
            closeDialog,
            downloadFile,
            updateFile,
        }
    },
}
</script>