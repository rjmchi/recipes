<template>
    <div class="container">
        <h2>Recipes</h2>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li v-bind:class="[{disabled:!pagination.prev_page_url}]" class="page-item"><a class="page-link" href="#" @click="fetchRecipes(pagination.prev_page_url)">Previous</a></li>
                
                <li class="page-item disabled"><a class="page-link text-dark" href="#">Page {{pagination.current_page}} of {{pagination.last_page}}</a></li>
                
                <li v-bind:class="[{disabled:!pagination.next_page_url}]"  class="page-item"><a class="page-link" href="#" @click="fetchRecipes(pagination.next_page_url)">Next</a></li>
            </ul>
        </nav>    
        <form class="newrecipe" @submit.prevent="addRecipe">
            <div class="form-group">
                <label for="name">Name: </label>
            	<input v-model="newRecipe.name" class="form-control" type="text" name="name">
            </div>

            <div class="form-row">
                <div class="col">
                    <label for="starter">Starter:</label>
                    <div class="input-group">
                        <input v-model="newRecipe.starter" type="text" name="starter" class="form-control">
                    </div>  
                </div> 

                <div class="col">
                    <label for="flour">Flour:</label>
                    <div class="input-group">
                        <input v-model="newRecipe.flour" type="text" name="flour" class="form-control">
                </div>  
            </div>             
                <div class="col">
                    <label for="water">Water:</label>
                    <div class="input-group">
                        <input v-model="newRecipe.water" type="text" name="water" class="form-control">
                </div>  
            </div>                 
                <div class="col">
                    <label for="salt">Salt:</label>
                    <div class="input-group">
                        <input v-model="newRecipe.salt" type="text" name="salt" class="form-control">
                </div>  
            </div>              
</div>
            <div class="form-group">
	            <label for="notes">Notes:</label>
	            <textarea v-model="newRecipe.notes" class="form-control" name="notes"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

        <div class="card card-body" v-for="(recipe, idx) in recipes" v-bind:key="idx">

            <h3>{{recipe.name}} &mdash; {{recipe.id}}</h3>
            <adjustable-value name="Starter" v-bind:initValue='recipe.starter' v-bind:idx='idx' @update-value="updateValue"/>
            <adjustable-value name="Flour" v-bind:initValue='recipe.flour' v-bind:idx='idx' @update-value="updateValue"/>
            <adjustable-value name="Water" v-bind:initValue='recipe.water' v-bind:idx='idx' @update-value="updateValue"/>

			<p>Salt: {{recipe.salt}}</p> 

            <div v-for="ingredient in recipe.ingredients" v-bind:key="ingredient.id">
                <p>{{ingredient.name}} {{ingredient.amount}} {{ingredient.unit}} <button class="btn btn-danger btn-sm" @click="removeIngredient(ingredient.id)">Delete</button></p>
            </div>             
            <p>Notes: {{recipe.notes}}</p> 
			<p>Hydration: {{((recipe.starter/2) + (recipe.water*1)) / ((recipe.starter/2) + (recipe.flour*1))}}</p>
             <form class="newingredient" @submit.prevent="addIngredient(recipe.id)">
                <span>
                <label for="name">Name:</label>
                <input v-model="ingredient.name" type="text" name="name">
                </span>
                <span>
                <label for="amount">Amount:</label>
                <input v-model="ingredient.amount" type="text" name="amount">
                </span>
                <span>
                <label for="unit">Unit:</label>
                <input v-model="ingredient.unit" type="text" name="unit">
                </span>
                <button type="submit" class="btn btn-primary">Add Ingredient</button> 
            </form>

            <div>
                <button @click="editRecipe(recipe)" class="btn btn-info">Edit Recipe</button>

                <button @click="deleteRecipe(recipe.id)" class="btn btn-danger">Delete</button>        
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                recipes: [],
                newRecipe: {
                    id:'',
                    name: '',
                    flour: 0,
                    starter: 0,
                    water: 0, 
                    salt: 0,
                    notes:'',
                },
                ingredient: {
                    id:'',
                    name:'',
                    amount:'',
                    unit:'',
                    recipe_id:'',
                },
                pagination: {},
                edit:false,
            }
        },
        created() {
            this.fetchRecipes();
        },
        methods: {
            removeIngredient(id) {
                if (confirm("Are you sure?")){
                    fetch(`/api/ingredient/${id}`, {
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.fetchRecipes();
                    })
                    .catch(err=>console.log(err));
                }

},
            updateValue(resp) {
                switch (resp.name) {
                    case 'Starter':
                        this.recipes[resp.idx].starter = resp.value;
                        break;
                    case 'Flour':
                        this.recipes[resp.idx].flour = resp.value;
                        break;
                    case 'Water':
                        this.recipes[resp.idx].water = resp.value;
                        break;                                                
                }
            },
            fetchRecipes(page_url) {
                let vm=this;
                page_url = page_url || '/api/recipe'
                fetch(page_url)
                    .then(res=> res.json())
                    .then(res=> {
                        this.recipes= res.data;
                        vm.makePagination(res.meta, res.links);
                    })
                    .catch(err => console.log(err));
            },
            makePagination(meta, links) {
                let pagination =  {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev,
                }
                this.pagination= pagination;
            },
            addRecipe() {
                if (this.edit === false)
                {
                    fetch('/api/recipe', {
                        method:'post',
                        body: JSON.stringify(this.newRecipe),
                        headers: {
                            'content-type':'application/json'
                        }
                    })
                    .then (res=>res.json())
                    .then(data => {
                        this.newRecipe.name = '';
                        this.newRecipe.notes = '';
                        this.newRecipe.flour = '';
                        this.newRecipe.salt = '';
                        this.newRecipe.starter = '';
                        this.newRecipe.water = '';
                        this.fetchRecipes();
                    })
                    .catch(err => console.log(err));
                } else {
                    fetch('/api/recipe/'+this.newRecipe.id, {
                        method:'put',
                        body: JSON.stringify(this.newRecipe),
                        headers: {
                            'content-type':'application/json'
                        }
                    })
                    .then (res=>res.json())
                    .then(data => {
                        this.newRecipe.name = '';
                        this.newRecipe.notes = '';
                        this.newRecipe.flour = '';
                        this.newRecipe.salt = '';
                        this.newRecipe.starter = '';
                        this.newRecipe.water = '';
                        this.fetchRecipes();
                    })
                    .catch(err => console.log(err));                    
                }
            },
            editRecipe(recipe) {
                this.edit = true;
                this.newRecipe.id = recipe.id;
                this.newRecipe.name = recipe.name;
                this.newRecipe.notes = recipe.notes;
                this.newRecipe.starter = recipe.starter;
                this.newRecipe.flour = recipe.flour;
                this.newRecipe.water = recipe.water;
                this.newRecipe.salt = recipe.salt;
            },
            deleteRecipe(id) {
                if (confirm("Are you sure?")){
                    fetch(`/api/recipe/${id}`, {
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.fetchRecipes();
                    })
                    .catch(err=>console.log(err));
                }
            },
            addIngredient(id) {
                this.ingredient.recipe_id = id;
                fetch('/api/ingredient', {
                    method:'post',
                    body: JSON.stringify(this.ingredient),
                    headers: {
                        'content-type':'application/json'
                    }
                })
                .then (res=>res.json())
                .then(data => {
                    this.ingredient.name = '';
                    this.ingredient.amount = '';
                    this.ingredient.unit = '';
                    this.fetchRecipes();
                })
                .catch(err => console.log(err));
            },
        }
    }
</script>
<style scoped>
.newrecipe {
    width:95%;
    border:1px solid #333;
    border-radius: 10px;
    margin:10px auto;
    padding:10px;
}
label {
    font-weight: bold;
}
.card {
    margin:5px;
}
form.newingredient {
    margin:5px 0;
    padding:15px;
    background-color:rgb(233, 236, 245);
}
</style>
