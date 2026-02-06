<div class="space-y-10">
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight italic uppercase">Manajemen <span class="text-indigo-600">User</span></h1>
            <p class="text-gray-500 font-bold mt-1 uppercase text-[10px] tracking-[0.2em]">Kelola akses administrator dan member</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8 border-b border-gray-50 bg-gray-50/30">
            <div class="max-w-md relative group">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email..." 
                    class="w-full pl-12 pr-6 py-4 bg-white border-none rounded-2xl text-sm font-bold shadow-sm focus:ring-2 focus:ring-indigo-600">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Role</th>
                        <th class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Terdaftar</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 uppercase italic">{{ $user->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 lowercase italic">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">
                                            MEMBER
                                        </span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $user->created_at->format('d M Y') }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @can('user.manage')
                                    <button wire:click="edit({{ $user->id }})" 
                                            class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:bg-gray-50 transition-colors shadow-sm">
                                        Edit
                                    </button>
                                @else
                                    <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">VIEW ONLY</span>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-8 border-t border-gray-50">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Edit Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Overlay -->
            <div class="fixed inset-0 bg-gray-900/75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-black text-gray-900 uppercase italic tracking-tight mb-4" id="modal-title">
                                Edit User
                            </h3>
                            
                            <div class="space-y-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Nama</label>
                                    <input type="text" wire:model="name" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                                    @error('name') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Email</label>
                                    <input type="email" wire:model="email" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                                    @error('email') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Role</label>
                                    <select wire:model="role" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                                </div>

                                <!-- Password (Optional) -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Password (Isi jika ingin ubah)</label>
                                    <input type="password" wire:model="password" class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-600">
                                    @error('password') <span class="text-xs text-rose-500 font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="update" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest">
                        Simpan
                    </button>
                    <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
