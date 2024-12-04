export function useJobFormOptions() {
    const educationOptions = [
        { value: 'sd', label: 'SD' },
        { value: 'smp', label: 'SMP' },
        { value: 'slta/sma/smk', label: 'SLTA/SMA/SMK' },
        { value: 'd3', label: 'D3' },
        { value: 'd4', label: 'D4' },
        { value: 's1', label: 'S1' },
        { value: 's2', label: 'S2' },
        { value: 's3', label: 'S3' }
    ];

    const jobTypeOptions = [
        { value: 'full time', label: 'Full Time' },
        { value: 'part time', label: 'Part Time' },
        { value: 'contract', label: 'Contract' }
    ];

    return {
        educationOptions,
        jobTypeOptions
    };
}
