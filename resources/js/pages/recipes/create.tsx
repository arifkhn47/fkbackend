import AppLayout from "@/layouts/app-layout";
import { Form, Head } from "@inertiajs/react";
import RecipeLayout from "./recipe-layout";
import RecipeController from "@/actions/App/Http/Controllers/Web/Recipes/RecipeController";
import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function Create() {
    return (
        <>
            <Head title="Create Recipe" />
            <h1 className="sr-only">Create Recipe</h1>

            <Form
                {...RecipeController.store.form()}
                className="space-y-6"
            >
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-2">
                            <Label htmlFor="name">Name</Label>

                            <Input
                                id="name"
                                className="mt-1 block w-full"
                                name="name"
                                autoComplete="name"
                                placeholder="Recipe name"
                            />

                            <InputError
                                className="mt-1"
                                message={errors.name}
                            />
                        </div>
                        <div className="flex items-center gap-4">
                            <Button
                                disabled={processing}
                                data-test="update-profile-button"
                            >
                                Create
                            </Button>
                        </div>
                        <div className="grid gap-2">
                            <span className="text-xs font-semibold">Note: Make sure you have all the foods available in your food databse for this recipe.</span>
                        </div>
                    </>
                )}

            </Form>
        </>
    );
}

Create.layout = [AppLayout, RecipeLayout];