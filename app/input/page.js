'use client'
import { useState } from 'react'
import { supabase } from '@/lib/supabaseClient'

export default function InputMaterial() {
    const [idMaterial, setIdMaterial] = useState('')
    const [jumlah, setJumlah] = useState('')
    const [ket, setKet] = useState('')

    const handleSubmit = async (e) => {
        e.preventDefault()
        await supabase.from('transaksi').insert({
            id_material: idMaterial,
            jenis: 'IN',
            jumlah: Number(jumlah),
            keterangan: ket
        })
        alert('Input berhasil disimpan!')
        }

    return (
        <form onSubmit={handleSubmit}>
        <h1>Input Material Masuk</h1>
        <input placeholder="ID Material" value={idMaterial} onChange={e=> setIdMaterial(e.target.value)} />
        <input placeholder="Jumlah" type="number" onChange={e=> setJumlah(e.target.value)} />
        <input placeholder="Keterangan" onChange={e=>setKet(e.target.value)} />
        <button>Simpan</button>
    </form>
    )
}