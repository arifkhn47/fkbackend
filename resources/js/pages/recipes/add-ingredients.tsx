import FoodSearchInput from "@/components/food-search/FoodSearchInput";
import { Food } from "../foods/types";


export default function AddIngredients() {
    function handleFoodSelect(food: Food) {
        console.log("Selected food:", food);
        // pre-fill your Add Food form here
    }

    return (
        <div className="max-w-xl mx-auto p-6">
            <FoodSearchInput onFoodSelect={handleFoodSelect} />
        </div>
    );
}