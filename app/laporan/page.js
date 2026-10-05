'use client'
import { useState, useEffect } from 'react'
import { supabase } from '@/lib/supabaseClient'

export default function LaporanPage() {
    const [laporan, setLaporan] = useState([])
    const [loading, setLoading] = useState(true)
    const [tglAwal, setTglAwal] = useState('')
    const [tglAkhir, setTglAkhir] = useState('')
}
// Ambil data dari Supabase
const ambilLaporan = async () => {
    setLoading(true)
    let query = supabase
        .from('transaksi')
        .select('id, tanggal, jenis, jumlah, keterangan, material(nama_material)')
        .order('tanggal', { ascending: false })
}
// Filter tanggal kalau di isi
if (tglAwal) quary = query.gte('tanggal', tglAwal)
if (tglAkhir) query = query.lte('tanggal', tglAkhir)

const { data, error } = await query
