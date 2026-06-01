const nodemailer = require('nodemailer');

const REQUIRED_EMAIL_ENV = ['GMAIL_USER', 'GMAIL_APP_PASSWORD', 'ADMIN_EMAIL'];

const getMissingEmailConfig = () => REQUIRED_EMAIL_ENV.filter((key) => !process.env[key]);

const isEmailConfigured = () => getMissingEmailConfig().length === 0;

let transporter;
let transporterSignature;

const getTransporter = () => {
  const signature = `${process.env.GMAIL_USER || ''}:${Boolean(process.env.GMAIL_APP_PASSWORD)}`;
  if (transporter && transporterSignature === signature) return transporter;

  if (!isEmailConfigured()) {
    const error = new Error(`Email service is not configured. Missing: ${getMissingEmailConfig().join(', ')}`);
    error.code = 'EMAIL_NOT_CONFIGURED';
    error.statusCode = 503;
    throw error;
  }

  transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
      user: process.env.GMAIL_USER,
      pass: process.env.GMAIL_APP_PASSWORD,
    },
  });
  transporterSignature = signature;

  return transporter;
};

const sendMail = async ({ to, subject, text, html }) => {
  const info = await getTransporter().sendMail({
    from: `"HAVOR SMARTA" <${process.env.GMAIL_USER}>`,
    to,
    subject,
    text,
    html,
  });

  return {
    sent: true,
    messageId: info.messageId,
  };
};

const sendMailSafely = async (mailOptions) => {
  try {
    return await sendMail(mailOptions);
  } catch (error) {
    if (process.env.NODE_ENV !== 'production') {
      console.error('Email delivery failed:', error.code || error.message);
    }

    return {
      sent: false,
      reason: error.code === 'EMAIL_NOT_CONFIGURED' ? 'missing_config' : 'delivery_failed',
    };
  }
};

const sendCareerEmails = async ({ application, resumeReference }) => {
  const userEmail = await sendMailSafely({
    to: application.email,
    subject: 'Lamaran Berhasil Dikirim - HAVOR SMARTA',
    text: [
      `Halo ${application.fullName},`,
      '',
      `Terima kasih telah mengirimkan lamaran untuk posisi ${application.position} di HAVOR SMARTA.`,
      '',
      'Lamaran Anda telah berhasil kami terima. Mohon tunggu sebentar, admin akan meninjau lamaran Anda dan membalas melalui email ini jika terdapat informasi lanjutan.',
      '',
      'Terima kasih,',
      'HAVOR SMARTA Team',
    ].join('\n'),
  });

  const adminEmail = await sendMailSafely({
    to: process.env.ADMIN_EMAIL,
    subject: `Lamaran Baru - ${application.position} - ${application.fullName}`,
    text: [
      'Ada lamaran baru yang masuk melalui website HAVOR SMARTA.',
      '',
      `Nama: ${application.fullName}`,
      `Email: ${application.email}`,
      `No. Telepon: ${application.phone}`,
      `Alamat: ${application.address || '-'}`,
      `Posisi: ${application.position}`,
      `Pendidikan: ${application.latestEducation || '-'}`,
      `Pengalaman: ${application.experienceSummary || '-'}`,
      `Portfolio: ${application.portfolioUrl || '-'}`,
      '',
      'Pesan/Cover Letter:',
      application.message,
      '',
      'CV:',
      resumeReference || '-',
    ].join('\n'),
  });

  return { userEmail, adminEmail };
};

const sendContactEmails = async ({ message }) => {
  const userEmail = await sendMailSafely({
    to: message.email,
    subject: 'Pesan Berhasil Dikirim - HAVOR SMARTA',
    text: [
      `Halo ${message.name},`,
      '',
      'Terima kasih telah menghubungi HAVOR SMARTA.',
      '',
      'Pesan Anda telah berhasil kami terima. Mohon tunggu sebentar, admin akan membalas melalui email ini secepatnya.',
      '',
      'Terima kasih,',
      'HAVOR SMARTA Team',
    ].join('\n'),
  });

  const adminEmail = await sendMailSafely({
    to: process.env.ADMIN_EMAIL,
    subject: `Pesan Baru dari Website - ${message.subject}`,
    text: [
      'Ada pesan baru yang masuk melalui website HAVOR SMARTA.',
      '',
      `Nama: ${message.name}`,
      `Email: ${message.email}`,
      `Subject: ${message.subject}`,
      '',
      'Pesan:',
      message.message,
    ].join('\n'),
  });

  return { userEmail, adminEmail };
};

module.exports = {
  getMissingEmailConfig,
  isEmailConfigured,
  sendCareerEmails,
  sendContactEmails,
  sendMail,
  sendMailSafely,
};
