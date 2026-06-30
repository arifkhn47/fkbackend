import { useState, useEffect, useRef } from "react";
import {
    Command,
    CommandInput,
    CommandList,
    CommandEmpty,
    CommandGroup,
    CommandItem,
} from "@/components/ui/command";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { Search } from "lucide-react";
import { Food } from "@/pages/foods/types";
import { useHttp } from "@inertiajs/react";
import FoodController from "@/actions/App/Http/Controllers/Api/V1/Foods/FoodController";



interface FoodSearchInputProps {
    onFoodSelect?: (food: Food) => void;
}

interface NutritionBadgeProps {
    label: string;
    value: number | null;
    unit?: string;
}

interface FoodResultCardProps {
    food: Food;
    onSelect: (food: Food) => void;
}

// ─── Nutrition Badge ────────────────────────────────────────────
function NutritionBadge({ label, value, unit = "g" }: NutritionBadgeProps) {
    return (
        <Badge variant="secondary" className="text-xs font-normal">
            {label}: {value ?? "—"}{unit}
        </Badge>
    );
}

// ─── Skeleton Loader ─────────────────────────────────────────────
function FoodSearchSkeleton() {
    return (
        <div className="space-y-2 p-2">
            {[1, 2, 3].map((i) => (
                <div key={i} className="flex flex-col gap-2 p-2">
                    <Skeleton className="h-4 w-1/2" />
                    <div className="flex gap-2">
                        <Skeleton className="h-3 w-16" />
                        <Skeleton className="h-3 w-16" />
                        <Skeleton className="h-3 w-16" />
                    </div>
                </div>
            ))}
        </div>
    );
}

// ─── Single Result Card ──────────────────────────────────────────
function FoodResultCard({ food, onSelect }: FoodResultCardProps) {
    return (
        <CommandItem
            value={food.name}
            onSelect={() => onSelect(food)}
            className="flex flex-col items-start gap-1 cursor-pointer px-3 py-2"
        >
            <div className="flex justify-between w-full">
                <span className="font-medium text-sm">{food.name}</span>
                <span className="text-xs text-muted-foreground">{food.category}</span>
            </div>
            <div className="flex flex-wrap gap-1">
                <NutritionBadge label="Cal" value={food.calories} unit="kcal" />
                <NutritionBadge label="Protein" value={food.protein} />
                <NutritionBadge label="Carbs" value={food.carbs} />
                <NutritionBadge label="Fat" value={food.fats} />
            </div>
        </CommandItem>
    );
}

// ─── Main Component ──────────────────────────────────────────────
export default function FoodSearchInput({ onFoodSelect }: FoodSearchInputProps) {
    const [open, setOpen] = useState<boolean>(false);
    const [query, setQuery] = useState<string>("");
    const [results, setResults] = useState<Food[]>([]);
    const [isLoading, setIsLoading] = useState<boolean>(false);
    const [error, setError] = useState<string | null>(null);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const { get } = useHttp()

    useEffect(() => {
        // Clear previous timer on every keystroke
        if (debounceRef.current) clearTimeout(debounceRef.current);

        // Reset state if query is cleared
        if (!query.trim()) {
            setResults([]);
            setError(null);
            return;
        }

        // Fire search after 500ms of no typing
        debounceRef.current = setTimeout(async () => {
            setIsLoading(true);
            setError(null);

            try {
                // const response = await axios.get<Food[]>("/food/search", {
                //     params: { q: query },
                // });
                console.log(get(FoodController.index.url()))
                // setResults();
            } catch (err) {
                setError("Something went wrong. Please try again.");
                setResults([]);
            } finally {
                setIsLoading(false);
            }
        }, 500);

        // Cleanup on unmount
        return () => {
            if (debounceRef.current) clearTimeout(debounceRef.current);
        };
    }, [query]);

    function handleSelect(food: Food) {
        setOpen(false);
        setQuery("");
        setResults([]);
        onFoodSelect?.(food); // pass selected food up to parent
    }

    return (
        <Popover open={open} onOpenChange={setOpen}>
            {/* ── Trigger ── */}
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    className="w-full justify-start text-muted-foreground font-normal"
                >
                    <Search className="mr-2 h-4 w-4 shrink-0" />
                    Search food database...
                </Button>
            </PopoverTrigger>

            {/* ── Dropdown ── */}
            <PopoverContent
                className="w-[400px] p-0"
                align="start"
                onOpenAutoFocus={(e) => e.preventDefault()}
            >
                <Command shouldFilter={false}>
                    <CommandInput
                        placeholder="e.g. Avocado, Chicken Breast..."
                        value={query}
                        onValueChange={setQuery}
                    />

                    <CommandList>
                        {/* Loading */}
                        {isLoading && <FoodSearchSkeleton />}

                        {/* Error */}
                        {!isLoading && error && (
                            <div className="px-4 py-3 text-sm text-destructive">
                                {error}
                            </div>
                        )}

                        {/* Empty */}
                        {!isLoading && !error && query && results.length === 0 && (
                            <CommandEmpty>
                                No results for "{query}". Try a different name.
                            </CommandEmpty>
                        )}

                        {/* Results */}
                        {!isLoading && results.length > 0 && (
                            <CommandGroup heading="Results from Food Database">
                                {results.map((food, index) => (
                                    <FoodResultCard
                                        key={food.id ?? index}
                                        food={food}
                                        onSelect={handleSelect}
                                    />
                                ))}
                            </CommandGroup>
                        )}
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}