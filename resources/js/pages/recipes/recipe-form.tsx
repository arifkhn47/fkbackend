import { usePage } from "@inertiajs/react";
import { Recipe } from "./types";

type PageProps = {
    recipe: Recipe;
};

export default function RecipeForm() {
    const { recipe } = usePage<PageProps>().props;

    return (
        <></>
    );
}