// Svelte 5 reactive cart store with localStorage persistence
class CartStore {
    items = $state([]);

    constructor() {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('cart_outdoor_species');
            if (saved) {
                try {
                    this.items = JSON.parse(saved);
                } catch (e) {
                    console.error("Failed to parse cart", e);
                    this.items = [];
                }
            }
        }
    }

    save() {
        if (typeof window !== 'undefined') {
            localStorage.setItem('cart_outdoor_species', JSON.stringify(this.items));
        }
    }

    addItem(gear, qty = 1) {
        if (!gear || gear.stock <= 0) return false;
        
        const existingIndex = this.items.findIndex(i => i.id === gear.id);
        if (existingIndex > -1) {
            const newQty = Math.min(gear.stock, this.items[existingIndex].quantity + qty);
            this.items[existingIndex].quantity = newQty;
        } else {
            this.items.push({
                id: gear.id,
                name: gear.name,
                price_per_day: gear.price_per_day,
                stock: gear.stock,
                image_path: gear.image_path || '',
                category: gear.category || 'Gear',
                quantity: Math.min(gear.stock, qty)
            });
        }
        this.save();
        return true;
    }

    removeItem(id) {
        this.items = this.items.filter(i => i.id !== id);
        this.save();
    }

    updateQuantity(id, qty) {
        const index = this.items.findIndex(i => i.id === id);
        if (index > -1) {
            const maxStock = this.items[index].stock || 1;
            if (qty <= 0) {
                this.removeItem(id);
            } else {
                this.items[index].quantity = Math.min(maxStock, qty);
                this.save();
            }
        }
    }

    clearCart() {
        this.items = [];
        if (typeof window !== 'undefined') {
            localStorage.removeItem('cart_outdoor_species');
        }
    }

    totalItems = $derived.by(() => {
        return this.items.reduce((acc, item) => acc + item.quantity, 0);
    });

    totalDailyRate = $derived.by(() => {
        return this.items.reduce((acc, item) => acc + (item.price_per_day * item.quantity), 0);
    });

    isInCart(id) {
        return this.items.some(i => i.id === id);
    }
}

export const cart = new CartStore();
