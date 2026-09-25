export function formatMoney(value) {
    const amount = Number(value)
    if (!Number.isFinite(amount)) {
        return '0'
    }
    return String(Number(amount.toFixed(2)))
}
