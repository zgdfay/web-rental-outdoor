<script>
    import { MapPin, Phone, Mail } from 'lucide-svelte';
</script>

<footer class="bg-zinc-950 text-zinc-400 py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 border-b border-zinc-900 pb-20">
            <!-- Brand -->
            <div class="space-y-6">
                <a href="/" class="flex items-center gap-2">
                    <div class="size-8 bg-primary/10 flex items-center justify-center rounded-md">
                        <img src='/assets/logo/logo-outdoor-species.png' alt='logo-outdoor-species'/>
                    </div>
                    <span class="text-xl font-bold text-white">
                        OUTDOOR <span class="text-primary">SPECIES</span>
                    </span>
                </a>
                <p class="text-sm leading-relaxed max-w-xs">
                    Premium outdoor gear rentals for every adventure. We simplify your journey by providing high-quality equipment without the burden of ownership.
                </p>
                <!-- <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-primary transition-colors"><Instagram size={20} /></a>
                    <a href="#" class="hover:text-primary transition-colors"><Facebook size={20} /></a>
                    <a href="#" class="hover:text-primary transition-colors"><Twitter size={20} /></a>
                    <a href="#" class="hover:text-primary transition-colors"><Youtube size={20} /></a>
                </div> -->
            </div>

            <!-- Shop -->
            <div>
                <h4 class="text-white font-semibold mb-6">Explore</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Camping Gear</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Hiking Essentials</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Climbing Kits</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Winter Sports</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">New Arrivals</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div>
                <h4 class="text-white font-semibold mb-6">Company</h4>
                <ul class="space-y-4 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">Our Story</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Sustainability</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Press</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="space-y-6">
                <h4 class="text-white font-semibold mb-6">Get in Touch</h4>
                <div class="space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <MapPin size={18} class="text-primary shrink-0" />
                        <span>123 Adventure Way,<br/>Mountain View, PNW 98101</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Phone size={18} class="text-primary shrink-0" />
                        <span>+1 (555) 123-4567</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Mail size={18} class="text-primary shrink-0" />
                        <span>hello@gearrental.com</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom -->
        <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-[12px] uppercase tracking-widest font-medium">
            <p>© 2026 GearRental Inc. All rights reserved.</p>
            <div class="flex items-center gap-8">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-white transition-colors">Cookie Settings</a>
            </div>
        </div>
    </div>
</footer>
