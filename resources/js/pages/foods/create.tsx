import FoodController from "@/actions/App/Http/Controllers/Web/Foods/FoodController";
import InputError from "@/components/input-error";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Form, Head } from "@inertiajs/react";
import FoodLayout from "./food-layout";
import AppLayout from "@/layouts/app-layout";

export default function Create() {

    return (
        <>
            <Head title="Add Food" />
            <h1 className="sr-only">Add Food</h1>
            
            <Form 
                {...FoodController.store.form() }
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
                                placeholder="Full name"
                            />

                            <InputError
                                className="mt-1"
                                message={errors.name}
                            />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="calories">Calories</Label>
                            <Input
                                id="calories"
                                name="calories"
                                type="number"
                                className="mt-1 block w-full"
                            />
                            <InputError message={errors.calories} className="mt-1" />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="protein">Protein</Label>
                            <Input
                                id="protein"
                                type="number"
                                name="protein"
                                className="mt-1 block w-full"
                            />
                            <InputError message={errors.protein} className="mt-1" />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="carbs">Carbs</Label>
                            <Input
                                id="carbs"
                                type="number"
                                name="carbs"
                                className="mt-1 block w-full"
                            />
                            <InputError message={errors.carbs} className="mt-1" />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="fats">Fat</Label>
                            <Input
                                id="fats"
                                name="fats"
                                type="number"
                                className="mt-1 block w-full"
                            />
                            <InputError message={errors.fats} className="mt-1" />
                        </div>
                        <div className="flex items-center gap-4">
                            <Button
                                disabled={processing}
                                data-test="update-profile-button"
                            >
                                Save
                            </Button>
                        </div>
                    </>
                )}

            </Form>
        </>
    );
}

Create.layout = [AppLayout, FoodLayout];