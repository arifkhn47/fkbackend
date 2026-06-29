import { useState } from "react";
import { Popover, PopoverContent, PopoverTrigger } from "../ui/popover";
import { Button } from "../ui/button";
import { Search } from "lucide-react";

export default function FoodSearchInput({ }) {
    const [open, setOpen] = useState(false);

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant={"outline"}
                    role="combobox"
                    className="w-full justify-start text-muted-foreground font-normal"
                >
                    <Search className="mr-2 h-4 w-4 shrink-0" />
                    Search food database...
                </Button>
            </PopoverTrigger>
            <PopoverContent
                className="w-[400px] p-0"
                align="start"
                onOpenAutoFocus={(e) => e.preventDefault()}
            ></PopoverContent>
        </Popover>
    );
}