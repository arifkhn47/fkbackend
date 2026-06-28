import AppLayout from "@/layouts/app-layout";
import FoodLayout from "./food-layout";
import { Food } from "./types";
import { usePage } from "@inertiajs/react";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { FOOD } from "@/constants/foods";
import TextLink from "@/components/text-link";
import { edit } from "@/routes/foods";

type PageProps = {
    foods: Food[];
};


export default function Index() {
    const { foods } = usePage<PageProps>().props;
    return (
        <>
        <Card>
            <CardHeader>
                <CardTitle>{FOOD.LIST_PAGE_TITLE}</CardTitle>
            </CardHeader>
            <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>#</TableHead>
                                <TableHead>Food</TableHead>
                                <TableHead className="text-right">Calories</TableHead>
                                <TableHead className="text-right">Protein</TableHead>
                                <TableHead className="text-right">Carbs</TableHead>
                                <TableHead className="text-right">Fat</TableHead>
                                <TableHead className="text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody>
                            {foods.map((food, index) => (
                                <TableRow key={food.id}>
                                    <TableCell>{index + 1}</TableCell>
                                    <TableCell>{food.name}</TableCell>
                                    <TableCell className="text-right">
                                        {food.calories}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {food.protein}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {food.carbs}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {food.fats}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        <TextLink href={edit(food)}>Edit</TextLink>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </>
    )
}

Index.layout = [AppLayout, FoodLayout];