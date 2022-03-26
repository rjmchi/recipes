<template>
<div>
    <p>{{name}}: {{value}}
        <button @click="increase()">+</button>
        <button @click="decrease()">-</button>
    </p> 
</div>
</template>
<script>
export default {
    props: ['name', 'value', 'idx'],
    data() {
        return {
            // value: this.initValue,
        }
    },
    methods: {
        increase() {
            this.value++;
            this.$emit('update-value', {name: this.name, value:this.value, idx:this.idx});
        },
        decrease() {
            this.value--;
            this.$emit('update-value', {name: this.name, value:this.value, idx:this.idx});
        }
    },
}
</script>
<style scoped>
    p {
        margin:2px;
    }
</style>